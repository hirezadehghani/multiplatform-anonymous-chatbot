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
        logger()->info('Incoming message', [
            'platform' => $platform->value,
            'platform_user_id' => $platformUserId,
            'text' => $text,
        ]);

        $account = UserAccount::query()
            ->where('platform', $platform)
            ->where('platform_user_id', $platformUserId)
            ->first();

        if ($account === null) {
            logger()->info('Found user', ['platform' => $platform->value, 'platform_user_id' => $platformUserId, 'found' => false]);
            return null;
        }

        $user = $account->user;
        logger()->info('Found user', ['user_id' => $user->id]);

        $room = $this->chatRoomService->activeRoomForUser($user);

        if ($room === null) {
            logger()->info('Found room', ['found' => false, 'user_id' => $user->id]);
            // Inform user there is no active chat
            $this->sendPlatformMessage($platform, $platformUserId, 'شما در حال حاضر در هیچ چتی نیستید.');
            return null; // no active chat — do not save or forward
        }

        logger()->info('Found room', ['room_id' => $room->id]);

        $message = $this->messageService->sendMessage($room, $user, $text);

        logger()->info('Saved message id', ['message_id' => $message->id]);

        $partnerId = $room->user1_id === $user->id ? $room->user2_id : $room->user1_id;
        logger()->info('Partner id', ['partner_id' => $partnerId]);

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
            logger()->info('Outgoing message', ['to_platform' => $platformKey, 'to_user_id' => $partnerAccount->platform_user_id, 'text' => $text]);
            $this->connectors[$platformKey]->sendMessage($partnerAccount->platform_user_id, $text);
        }

        return $message;
    }

    public function sendPlatformMessage(PlatformEnum $platform, string $platformUserId, string $message): void
    {
        logger()->info('Outgoing message', [
            'platform' => $platform->value,
            'user_id' => $platformUserId,
            'message' => $message,
        ]);
        $platformKey = $platform->value;

        if (isset($this->connectors[$platformKey])) {
            $this->connectors[$platformKey]->sendMessage($platformUserId, $message);
        }
    }
}
