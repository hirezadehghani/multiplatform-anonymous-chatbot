<?php

namespace App\Services\Matchmaking;

use App\Enums\UserStatusEnum;
use App\Models\ChatRoom;
use App\Models\User;
use App\Services\Chat\ChatService;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Redis\Connections\Connection;
use InvalidArgumentException;

class MatchmakingService
{
    public function __construct(
        private readonly ChatService $chatService,
        private readonly RedisFactory $redis,
    ) {}

    public function enterQueue(User $user, string $mode = 'any'): void
    {
        $this->ensureCanEnterQueue($user);

        $connection = $this->connection();
        $userId = (string) $user->id;

        $queue = $this->queueKey($mode);
        $waiting = $this->waitingKey($mode);

        if ($connection->sismember($waiting, $userId)) {
            return;
        }

        $connection->pipeline(function ($pipe) use ($queue, $waiting, $userId): void {
            $pipe->lpush($queue, $userId);
            $pipe->sadd($waiting, $userId);
        });

        $this->setUserStatus($user, UserStatusEnum::SEARCHING);
    }

    public function leaveQueue(User $user, string $mode = 'any'): void
    {
        $userId = (string) $user->id;
        $connection = $this->connection();
        $connection->pipeline(function ($pipe) use ($userId, $mode): void {
            $pipe->lrem($this->queueKey($mode), 0, $userId);
            $pipe->srem($this->waitingKey($mode), $userId);
        });

        $this->setUserStatus($user, UserStatusEnum::OFFLINE);
    }

    public function findPartner(User $user, string $mode = 'any'): ?ChatRoom
    {
        $this->ensureCanEnterQueue($user);

        $queue = $this->queueKey($mode);

        $partnerId = $this->connection()->eval($this->matchPartnerScript(), 1, $queue, (string) $user->id);

        if ($partnerId === null || $partnerId === false) {
            $this->markUserAsWaiting($user, $mode);
            return null;
        }

        $partner = User::query()->find((int) $partnerId);

        if ($partner === null) {
            $this->removeWaitingMember((string) $partnerId, $mode);
            return null;
        }

        $this->removeWaitingMember((string) $user->id, $mode);
        $this->removeWaitingMember((string) $partner->id, $mode);

        return $this->chatService->startChat($user, $partner);
    }

    private function ensureCanEnterQueue(User $user): void
    {
        if ($user->status === UserStatusEnum::CHATTING) {
            throw new InvalidArgumentException('User is already in an active chat.');
        }
    }

    private function markUserAsWaiting(User $user, string $mode): void
    {
        $this->connection()->sadd($this->waitingKey($mode), (string) $user->id);
        $this->setUserStatus($user, UserStatusEnum::SEARCHING);
    }

    private function removeWaitingMember(string $userId, string $mode): void
    {
        $this->connection()->srem($this->waitingKey($mode), $userId);
    }

    private function setUserStatus(User $user, UserStatusEnum $status): void
    {
        User::query()
            ->whereKey($user->id)
            ->update(['status' => $status]);

        $user->status = $status;
    }

    private function connection()
    {
        return $this->redis->connection((string) config('matchmaking.redis_connection', 'default'));
    }

    private function queueKey(string $mode): string
    {
        $base = (string) config('matchmaking.queue_key', 'matchmaking:queue');
        return $mode === 'any' ? $base : $base . ':' . $mode;
    }

    private function waitingKey(string $mode): string
    {
        $base = (string) config('matchmaking.waiting_key', 'matchmaking:waiting');
        return $mode === 'any' ? $base : $base . ':' . $mode;
    }

    private function matchPartnerScript(): string
    {
        return <<<'LUA'
local queueKey = KEYS[1]
local userId = ARGV[1]

redis.call('LREM', queueKey, 0, userId)

local partnerId = redis.call('RPOP', queueKey)

while partnerId do
    if partnerId ~= userId then
        return partnerId
    end

    partnerId = redis.call('RPOP', queueKey)
end

redis.call('LPUSH', queueKey, userId)

return nil
LUA;
    }
}
