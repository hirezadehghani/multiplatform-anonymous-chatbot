<?php

namespace App\Services\Bale;

use App\DTOs\Bale\BaleUpdate;
use App\Models\Bot;
use App\Models\UserAccount;
use App\Services\Platform\UserAccountResolver;
use App\Services\Chat\BotCommandRouter;
use App\Services\Chat\PreviousChatsService;
use App\Services\Chat\MessageRelayService as ChatMessageRelayService;
use App\Enums\PlatformEnum;

class BaleMessageHandler
{
    public function __construct(
        private readonly UserAccountResolver $userAccountResolver,
        private readonly BotCommandRouter $router,
        private readonly PreviousChatsService $previousChatsService,
        private readonly ChatMessageRelayService $messageRelayService,
    ) {}

    public function handle(Bot $bot, BaleUpdate $update): void
    {
        logger()->info('Incoming update', [
            'text' => $update->message,
            'user_id' => $update->platformUserId(),
        ]);

        if (! $update->hasUserInteraction()) {
            return;
        }

        $account = $this->resolveUserAccount($bot, $update);

        $text = trim((string) ($update->text() ?? ''));

        $route = $this->router->route($text);
        logger()->info('Route result', [
            'route' => $route?->command?->value,
        ]);

        // If incoming text is not a bot command, treat it as a chat message
        if ($route === null) {
            $this->messageRelayService->handleIncomingMessage(PlatformEnum::BALE, $update->platformUserId(), $text);
            return;
        }

        // Handle previous chats command
        if ($route->command->value === 'previous_chats') {
            $parts = preg_split('/\s+/', $text, 2);
            $page = 1;
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $page = (int) $parts[1];
                if ($page < 1) {
                    $page = 1;
                }
            }

            $result = $this->previousChatsService->listForUser($account->user, 5, $page);

            logger()->info('Previous chats result', $result);

            if (empty($result['data'])) {
                $this->messageRelayService->sendPlatformMessage(PlatformEnum::BALE, $update->platformUserId(), 'چتی یافت نشد.');
                return;
            }

            $lines = array_map(function ($row) {
                return sprintf(
                    "%s — پیام‌ها: %d — شروع: %s — پایان: %s",
                    $row['partner_display_name'] ?? '—',
                    $row['messages_count'] ?? 0,
                    $row['started_at'] ?? '-',
                    $row['ended_at'] ?? '-'
                );
            }, $result['data']);

            $body = implode("\n", $lines) . "\n\n" . sprintf("صفحه %d از %d", $result['meta']['current_page'], $result['meta']['last_page']);

            $this->messageRelayService->sendPlatformMessage(PlatformEnum::BALE, $update->platformUserId(), $body);
        }
    }

    private function resolveUserAccount(Bot $bot, BaleUpdate $update): UserAccount
    {
        return $this->userAccountResolver->resolveOrCreate(
            bot: $bot,
            platformUserId: $update->platformUserId(),
            username: $update->username(),
            displayName: $update->displayName(),
        );
    }
}
