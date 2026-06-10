<?php

use App\Enums\ChatRoomStatusEnum;
use App\Enums\MessageTypeEnum;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use App\Services\Chat\PreviousChatsService;

uses(Tests\TestCase::class);

it('lists previous chats with stats and pagination', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();

    $room = ChatRoom::create([
        'user1_id' => $a->id,
        'user2_id' => $b->id,
        'status' => ChatRoomStatusEnum::ENDED,
        'started_at' => now()->subHour(),
        'ended_at' => now(),
    ]);

    Message::create([ 'room_id' => $room->id, 'sender_user_id' => $a->id, 'body' => 'one', 'type' => MessageTypeEnum::TEXT ]);
    Message::create([ 'room_id' => $room->id, 'sender_user_id' => $b->id, 'body' => 'two', 'type' => MessageTypeEnum::TEXT ]);
    Message::create([ 'room_id' => $room->id, 'sender_user_id' => $a->id, 'body' => 'three', 'type' => MessageTypeEnum::TEXT ]);

    $service = new PreviousChatsService();

    $result = $service->listForUser($a, 10, 1);

    expect($result['meta']['total'])->toBe(1);
    expect(count($result['data']))->toBe(1);
    expect($result['data'][0]['messages_count'])->toBe(3);
    expect($result['data'][0]['partner_display_name'])->toBe($b->display_name);
});
