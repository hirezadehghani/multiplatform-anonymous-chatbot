<?php

namespace App\Services\Match;

use App\Enums\ChatRoomStatusEnum;
use App\Enums\UserStatusEnum;
use App\Models\ChatRoom;
use App\Models\User;
use App\Services\Chat\ChatService;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Redis\Connections\Connection;
use InvalidArgumentException;
use RuntimeException;

class MatchService
{
    private const int MAX_PARTNER_LOOKUP_ATTEMPTS = 10;

    public function __construct(
        private readonly ChatService $chatService,
        private readonly RedisFactory $redis,
    ) {}

    public function putInQueue(User $user): void
    {
        $this->ensureCanEnterQueue($user);

        $connection = $this->redisConnection();
        $userId = (string) $user->id;

        if ($connection->sismember($this->waitingKey(), $userId)) {
            return;
        }

        $connection->pipeline(function ($pipe) use ($userId): void {
            $pipe->lpush($this->queueKey(), $userId);
            $pipe->sadd($this->waitingKey(), $userId);
        });

        $this->setUserStatus($user, UserStatusEnum::SEARCHING);
    }

    public function removeFromQueue(User $user): void
    {
        $userId = (string) $user->id;

        $this->redisConnection()->pipeline(function ($pipe) use ($userId): void {
            $pipe->lrem($this->queueKey(), 0, $userId);
            $pipe->srem($this->waitingKey(), $userId);
        });

        $this->setUserStatus($user, UserStatusEnum::OFFLINE);
    }

    public function findPartner(User $user): ?ChatRoom
    {
        $this->ensureCanEnterQueue($user);

        $attempts = 0;

        while ($attempts < self::MAX_PARTNER_LOOKUP_ATTEMPTS) {
            $attempts++;

            $partnerId = $this->redisConnection()->eval(
                $this->matchPartnerScript(),
                1,
                $this->queueKey(),
                (string) $user->id,
            );

            if ($partnerId === null || $partnerId === false) {
                $this->markUserAsWaiting($user);

                return null;
            }

            $partner = User::query()->find((int) $partnerId);

            if ($partner === null) {
                $this->removeWaitingMember((string) $partnerId);

                continue;
            }

            $this->removeWaitingMember((string) $user->id);
            $this->removeWaitingMember((string) $partner->id);

            return $this->chatService->startChat($user, $partner);
        }

        throw new RuntimeException('Unable to find a valid partner after multiple attempts.');
    }

    private function ensureCanEnterQueue(User $user): void
    {
        if ($user->status === UserStatusEnum::CHATTING) {
            throw new InvalidArgumentException('User is already in an active chat.');
        }

        if ($this->hasActiveChatRoom($user)) {
            throw new InvalidArgumentException('User is already in an active chat.');
        }
    }

    private function hasActiveChatRoom(User $user): bool
    {
        return ChatRoom::query()
            ->where('status', ChatRoomStatusEnum::ACTIVE)
            ->where(function ($query) use ($user): void {
                $query
                    ->where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id);
            })
            ->exists();
    }

    private function markUserAsWaiting(User $user): void
    {
        $this->redisConnection()->sadd($this->waitingKey(), (string) $user->id);
        $this->setUserStatus($user, UserStatusEnum::SEARCHING);
    }

    private function removeWaitingMember(string $userId): void
    {
        $this->redisConnection()->srem($this->waitingKey(), $userId);
    }

    private function setUserStatus(User $user, UserStatusEnum $status): void
    {
        User::query()
            ->whereKey($user->id)
            ->update(['status' => $status]);

        $user->status = $status;
    }

    private function redisConnection(): Connection
    {
        return $this->redis->connection(
            (string) config('matchmaking.redis_connection', 'default'),
        );
    }

    private function queueKey(): string
    {
        return (string) config('matchmaking.queue_key', 'matchmaking:queue');
    }

    private function waitingKey(): string
    {
        return (string) config('matchmaking.waiting_key', 'matchmaking:waiting');
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
