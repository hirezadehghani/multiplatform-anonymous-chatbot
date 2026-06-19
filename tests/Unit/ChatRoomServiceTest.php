<?php

use App\Models\User;
use App\Services\Chat\ChatRoomService;
use App\Enums\ChatRoomStatusEnum;
use App\Enums\UserStatusEnum;

uses(Tests\TestCase::class);

it('starts and ends chat', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();

    $service = new ChatRoomService();

    $room = $service->startChat($a, $b);

    expect($room->status->value ?? $room->status)->toBe(ChatRoomStatusEnum::ACTIVE->value);

    $a->refresh();
    $b->refresh();

    expect($a->status)->toBe(UserStatusEnum::CHATTING->value);
    expect($b->status)->toBe(UserStatusEnum::CHATTING->value);

    $room = $service->endChat($room);

    expect($room->status->value ?? $room->status)->toBe(ChatRoomStatusEnum::ENDED->value);

    $a->refresh();
    expect($a->status)->toBe(UserStatusEnum::OFFLINE->value);
});

it('skips partner by ending active chat', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();

    $service = new ChatRoomService();

    $room = $service->startChat($a, $b);

    $result = $service->skipPartner($a);

    expect($result)->toBeTrue();

    $room->refresh();
    expect($room->status->value ?? $room->status)->toBe(ChatRoomStatusEnum::ENDED->value);
});
