<?php

use App\Models\User;
use App\Services\Chat\ChatRoomService;
use App\Services\Chat\MessageService;
use App\Enums\MessageTypeEnum;

uses(Tests\TestCase::class);

it('stores text messages for active chat', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();

    $roomService = new ChatRoomService();
    $msgService = new MessageService();

    $room = $roomService->startChat($a, $b);

    $message = $msgService->sendMessage($room, $a, 'hello');

    expect($message->body)->toBe('hello');
    expect($message->sender_user_id)->toBe($a->id);
    expect($message->type->value ?? $message->type)->toBe(MessageTypeEnum::TEXT->value);
});

it('rejects sending empty messages', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();

    $roomService = new ChatRoomService();
    $msgService = new MessageService();

    $room = $roomService->startChat($a, $b);

    $this->expectException(InvalidArgumentException::class);

    $msgService->sendMessage($room, $a, '   ');
});
