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
    public function startChat(User $user1, User $user2): ChatRoom
    {
        if ($user1->is($user2)) {
            throw new InvalidArgumentException('A user cannot start a chat with themselves.');
        }

        if ($this->activeRoomForUser($user1) !== null) {
            throw new InvalidArgumentException('User is already in an active chat.');
        }

        if ($this->activeRoomForUser($user2) !== null) {
            throw new InvalidArgumentException('Partner is already in an active chat.');
        }

        return DB::transaction(function () use ($user1, $user2): ChatRoom {
            $room = ChatRoom::query()->create([
                'user1_id' => $user1->id,
                'user2_id' => $user2->id,
                'status' => ChatRoomStatusEnum::ACTIVE,
                'started_at' => now(),
            ]);

            User::query()
                ->whereIn('id', [$user1->id, $user2->id])
                ->update(['status' => UserStatusEnum::CHATTING]);

            return $room->fresh();
        });
    }

    public function sendMessage(
        ChatRoom $room,
        User $sender,
        string $body,
        MessageTypeEnum $type = MessageTypeEnum::TEXT,
        ?array $meta = null,
    ): Message {
        $this->ensureRoomIsActive($room);
        $this->ensureUserIsParticipant($room, $sender);

        if ($type === MessageTypeEnum::TEXT && trim($body) === '') {
            throw new InvalidArgumentException('Text messages must have a body.');
        }

        return Message::query()->create([
            'room_id' => $room->id,
            'sender_user_id' => $sender->id,
            'body' => $body,
            'type' => $type,
            'meta' => $meta,
        ]);
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
