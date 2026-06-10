<?php

namespace App\Services\Chat;

use App\Contracts\PlatformConnector;
use App\Enums\PlatformEnum;
use App\Models\UserAccount;
use App\Models\Message;

final class MessageRelayService
{
    /** @var array<string, PlatformConnector> */
    private array $connectors = [];

    public function __construct(
        iterable $connectors,
        private readonly ChatRoomService $chatRoomService,
        private readonly MessageService $messageService,
    ) {
        foreach ($connectors as $connector) {
            $this->connectors[$connector->getPlatform()->value] = $connector;
        }
    }

    public function handleIncomingMessage(PlatformEnum $platform, string $platformUserId, string $text): ?Message
    {
        $account = UserAccount::query()
            ->where('platform', $platform)
            ->where('platform_user_id', $platformUserId)
            ->first();

        if ($account === null) {
            return null;
        }

        $user = $account->user;

        $room = $this->chatRoomService->activeRoomForUser($user);

        if ($room === null) {
            return null; // no active chat — do not save or forward
        }

        $message = $this->messageService->sendMessage($room, $user, $text);

        $partnerId = $room->user1_id === $user->id ? $room->user2_id : $room->user1_id;

        $partnerAccount = UserAccount::query()
            ->where('user_id', $partnerId)
            ->where('is_primary', true)
            ->first()
            ?? UserAccount::query()->where('user_id', $partnerId)->first();

        if ($partnerAccount === null) {
            return $message;
        }

        $platformKey = $partnerAccount->platform->value;

        if (isset($this->connectors[$platformKey])) {
            $this->connectors[$platformKey]->sendMessage($partnerAccount->platform_user_id, $text);
        }

        return $message;
    }
}
