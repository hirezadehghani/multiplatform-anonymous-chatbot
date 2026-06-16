<?php

namespace App\Services\Chat;

use App\Enums\ChatRoomStatusEnum;
use App\Enums\UserStatusEnum;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ChatRoomService
{
    public function startChat(User $userA, User $userB): ChatRoom
    {
        if ($userA->is($userB)) {
            throw new InvalidArgumentException('A user cannot start a chat with themselves.');
        }

        if ($this->activeRoomForUser($userA) !== null) {
            throw new InvalidArgumentException('User is already in an active chat.');
        }

        if ($this->activeRoomForUser($userB) !== null) {
            throw new InvalidArgumentException('Partner is already in an active chat.');
        }

        return DB::transaction(function () use ($userA, $userB): ChatRoom {
            $room = ChatRoom::query()->create([
                'user1_id' => $userA->id,
                'user2_id' => $userB->id,
                'status' => ChatRoomStatusEnum::ACTIVE,
                'started_at' => now(),
            ]);

            User::query()
                ->whereIn('id', [$userA->id, $userB->id])
                ->update(['status' => UserStatusEnum::CHATTING]);

            return $room->fresh();
        });
    }

    public function endChat(ChatRoom $room): ChatRoom
    {
        $this->ensureRoomIsActive($room);

        return DB::transaction(function () use ($room): ChatRoom {
            $room->update([
                'status' => ChatRoomStatusEnum::ENDED,
                'ended_at' => now(),
            ]);

            User::query()
                ->whereIn('id', [$room->user1_id, $room->user2_id])
                ->update(['status' => UserStatusEnum::OFFLINE]);

            return $room->fresh();
        });
    }

    /**
     * Skip partner: ends the current active room for the user and returns whether an active room existed.
     */
    public function skipPartner(User $user): bool
    {
        $room = $this->activeRoomForUser($user);

        if ($room === null) {
            return false;
        }

        $this->endChat($room);

        return true;
    }

    public function activeRoomForUser(User $user): ?ChatRoom
    {
        return ChatRoom::query()
            ->where('status', ChatRoomStatusEnum::ACTIVE)
            ->where(function ($query) use ($user): void {
                $query
                    ->where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id);
            })
            ->first();
    }

    private function ensureRoomIsActive(ChatRoom $room): void
    {
        if ($room->status !== ChatRoomStatusEnum::ACTIVE) {
            throw new InvalidArgumentException('Chat room is not active.');
        }
    }
}
