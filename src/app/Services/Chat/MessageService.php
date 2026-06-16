<?php

namespace App\Services\Chat;

use App\Enums\MessageTypeEnum;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MessageService
{
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

    private function ensureRoomIsActive(ChatRoom $room): void
    {
        if ($room->status !== \App\Enums\ChatRoomStatusEnum::ACTIVE) {
            throw new InvalidArgumentException('Chat room is not active.');
        }
    }

    private function ensureUserIsParticipant(ChatRoom $room, User $user): void
    {
        if (! in_array($user->id, [$room->user1_id, $room->user2_id], true)) {
            throw new InvalidArgumentException('User is not a participant in this chat room.');
        }
    }
}
