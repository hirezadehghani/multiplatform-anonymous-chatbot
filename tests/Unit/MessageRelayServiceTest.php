<?php

use App\Enums\PlatformEnum;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\Chat\ChatRoomService;
use App\Services\Chat\MessageService;
use App\Services\Chat\MessageRelayService;

uses(Tests\TestCase::class);

it('forwards message to partner using connector and saves message', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();

    $bot = \App\Models\Bot::query()->create([
        'name' => 'test-bot',
        'platform' => PlatformEnum::WEB,
        'token' => 'x',
        'is_active' => true,
    ]);

    $aAccount = UserAccount::query()->create([
        'user_id' => $a->id,
        'bot_id' => $bot->id,
        'platform' => PlatformEnum::TELEGRAM,
        'platform_user_id' => 'tg:'.$a->id,
        'is_primary' => true,
    ]);

    $bAccount = UserAccount::query()->create([
        'user_id' => $b->id,
        'bot_id' => $bot->id,
        'platform' => PlatformEnum::BALE,
        'platform_user_id' => 'bale:'.$b->id,
        'is_primary' => true,
    ]);

    $roomService = new ChatRoomService();
    $msgService = new MessageService();

    $room = $roomService->startChat($a, $b);

    $baleConnector = new class(PlatformEnum::BALE) implements App\Contracts\PlatformConnector {
        public array $sent = [];
        private \App\Enums\PlatformEnum $platform;
        public function __construct(\App\Enums\PlatformEnum $platform) { $this->platform = $platform; }
        public function sendMessage(string $platformUserId, string $message): void { $this->sent[] = ['to' => $platformUserId, 'message' => $message]; }
        public function getPlatform(): \App\Enums\PlatformEnum { return $this->platform; }
    };

    $relay = new MessageRelayService([
        $baleConnector,
    ], $roomService, $msgService);

    $message = $relay->handleIncomingMessage(PlatformEnum::TELEGRAM, $aAccount->platform_user_id, 'hello');

    expect($message)->not->toBeNull();
    expect($message->body)->toBe('hello');

    expect(count($baleConnector->sent))->toBe(1);
    expect($baleConnector->sent[0]['to'])->toBe($bAccount->platform_user_id);
    expect($baleConnector->sent[0]['message'])->toBe('hello');
});

it('does not send or save when no active chat exists', function () {
    $a = User::factory()->create();

    $bot = \App\Models\Bot::query()->create([
        'name' => 'test-bot',
        'platform' => PlatformEnum::WEB,
        'token' => 'x',
        'is_active' => true,
    ]);

    $aAccount = UserAccount::query()->create([
        'user_id' => $a->id,
        'bot_id' => $bot->id,
        'platform' => PlatformEnum::TELEGRAM,
        'platform_user_id' => 'tg:'.$a->id,
        'is_primary' => true,
    ]);

    $fakeConnector = new class(PlatformEnum::TELEGRAM) implements App\Contracts\PlatformConnector {
        public array $sent = [];
        private \App\Enums\PlatformEnum $platform;
        public function __construct(\App\Enums\PlatformEnum $platform) { $this->platform = $platform; }
        public function sendMessage(string $platformUserId, string $message): void { $this->sent[] = ['to' => $platformUserId, 'message' => $message]; }
        public function getPlatform(): \App\Enums\PlatformEnum { return $this->platform; }
    };

    $roomService = new ChatRoomService();
    $msgService = new MessageService();

    $relay = new MessageRelayService([$fakeConnector], $roomService, $msgService);

    $message = $relay->handleIncomingMessage(PlatformEnum::TELEGRAM, $aAccount->platform_user_id, 'hello');

    expect($message)->toBeNull();
    expect(\App\Models\Message::query()->count())->toBe(0);
    expect(count($fakeConnector->sent))->toBe(0);
});
