<?php

namespace App\Services\Chat;

use App\Enums\ChatRoomStatusEnum;
use App\Enums\MessageTypeEnum;
use App\Enums\UserStatusEnum;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ChatService
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

    public function sendMessage(ChatRoom $room, User $sender, string $text): Message
    {
        $this->ensureRoomIsActive($room);
        $this->ensureUserIsParticipant($room, $sender);

        if (trim($text) === '') {
            throw new InvalidArgumentException('Text messages must not be empty.');
        }

        return DB::transaction(function () use ($room, $sender, $text): Message {
            return Message::query()->create([
                'room_id' => $room->id,
                'sender_user_id' => $sender->id,
                'body' => $text,
                'type' => MessageTypeEnum::TEXT,
            ]);
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

    private function activeRoomForUser(User $user): ?ChatRoom
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

    private function ensureUserIsParticipant(ChatRoom $room, User $user): void
    {
        if (! $this->isParticipant($room, $user)) {
            throw new InvalidArgumentException('User is not a participant in this chat room.');
        }
    }

    private function isParticipant(ChatRoom $room, User $user): bool
    {
        return in_array($user->id, [$room->user1_id, $room->user2_id], true);
    }
}
