<?php

use App\Enums\PlatformEnum;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\Chat\ChatRoomService;
use App\Services\Chat\MessageService;
use App\Services\Chat\MessageRelayService;
use App\Models\ChatRoom;
use App\Services\Bale\BaleMessageHandler;
use App\DTOs\Bale\BaleUpdate;

beforeEach(function () {
    // nothing special
});

it('saves message and forwards to partner', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $bot = App\Models\Bot::query()->create(['name' => 'test', 'platform' => PlatformEnum::BALE->value, 'token' => 't1', 'is_active' => true]);

    $accountA = UserAccount::query()->create([
        'user_id' => $userA->id,
        'bot_id' => $bot->id,
        'platform' => PlatformEnum::BALE,
        'platform_user_id' => 'u_a',
        'is_primary' => true,
    ]);

    $accountB = UserAccount::query()->create([
        'user_id' => $userB->id,
        'bot_id' => $bot->id,
        'platform' => PlatformEnum::BALE,
        'platform_user_id' => 'u_b',
        'is_primary' => true,
    ]);

    $chatRoomService = $this->app->make(ChatRoomService::class);
    $messageService = $this->app->make(MessageService::class);

    $room = $chatRoomService->startChat($userA, $userB);

    $sent = [];
    $fakeConnector = new class($sent) implements \App\Contracts\PlatformConnector {
        public array $sent;
        public function __construct(array &$sent)
        {
            $this->sent = & $sent;
        }
        public function sendMessage(string $platformUserId, string $message): void
        {
            $this->sent[] = ['to' => $platformUserId, 'message' => $message];
        }
        public function getPlatform(): PlatformEnum
        {
            return PlatformEnum::BALE;
        }
    };

    $relay = new MessageRelayService([$fakeConnector], $chatRoomService, $messageService);

    $message = $relay->handleIncomingMessage(PlatformEnum::BALE, 'u_a', 'hello partner');

    expect($message)->not->toBeNull();
    expect($message->body)->toBe('hello partner');

    $sentList = $fakeConnector->sent;
    expect($sentList)->not->toBeEmpty();
    expect($sentList[0]['to'])->toBe('u_b');
    expect($sentList[0]['message'])->toBe('hello partner');
});

it('rejects message when no active room and informs sender', function () {
    $userA = User::factory()->create();

    $bot = App\Models\Bot::query()->create(['name' => 'test2', 'platform' => PlatformEnum::BALE->value, 'token' => 't2', 'is_active' => true]);

    $accountA = UserAccount::query()->create([
        'user_id' => $userA->id,
        'bot_id' => $bot->id,
        'platform' => PlatformEnum::BALE,
        'platform_user_id' => 'solo',
        'is_primary' => true,
    ]);

    $sent = [];
    $fakeConnector = new class($sent) implements \App\Contracts\PlatformConnector {
        public array $sent;
        public function __construct(array &$sent)
        {
            $this->sent = & $sent;
        }
        public function sendMessage(string $platformUserId, string $message): void
        {
            $this->sent[] = ['to' => $platformUserId, 'message' => $message];
        }
        public function getPlatform(): PlatformEnum
        {
            return PlatformEnum::BALE;
        }
    };

    $chatRoomService = $this->app->make(ChatRoomService::class);
    $messageService = $this->app->make(MessageService::class);

    $relay = new MessageRelayService([$fakeConnector], $chatRoomService, $messageService);

    $message = $relay->handleIncomingMessage(PlatformEnum::BALE, 'solo', 'hello?');

    expect($message)->toBeNull();

    $sentList = $fakeConnector->sent;
    expect($sentList)->not->toBeEmpty();
    expect($sentList[0]['to'])->toBe('solo');
    expect($sentList[0]['message'])->toBe('شما در حال حاضر در هیچ چتی نیستید.');
});

it('does not forward commands as chat messages', function () {
    $bot = App\Models\Bot::query()->create(['name' => 'cmd-bot', 'platform' => PlatformEnum::BALE->value, 'token' => 't3', 'is_active' => true]);

    // prepare update with a command
    $payload = [
        'update_id' => 1,
        'message' => [
            'from' => ['id' => 'cmd_user', 'username' => 'u'],
            'text' => '/previous_chats',
        ],
    ];

    $update = BaleUpdate::fromArray($payload);

    $sent = [];
    $fakeConnector = new class($sent) implements \App\Contracts\PlatformConnector {
        public array $sent;
        public function __construct(array &$sent)
        {
            $this->sent = & $sent;
        }
        public function sendMessage(string $platformUserId, string $message): void
        {
            $this->sent[] = ['to' => $platformUserId, 'message' => $message];
        }
        public function getPlatform(): PlatformEnum
        {
            return PlatformEnum::BALE;
        }
    };

    $realRelay = new MessageRelayService([$fakeConnector], $this->app->make(ChatRoomService::class), $this->app->make(MessageService::class));

    $handler = new BaleMessageHandler(
        $this->app->make(App\Services\Platform\UserAccountResolver::class),
        $this->app->make(App\Services\Chat\BotCommandRouter::class),
        $this->app->make(App\Services\Chat\PreviousChatsService::class),
        $realRelay,
    );

    $handler->handle($bot, $update);

    // Commands must not create a chat message record
    expect(App\Models\Message::query()->count())->toBe(0);
    expect($sent)->not->toBeEmpty();
});
