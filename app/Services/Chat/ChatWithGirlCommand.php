<?php

namespace App\Services\Chat;

use App\Enums\PlatformEnum;
use App\Services\Chat\UserRegistrationService;
use App\Services\Matchmaking\MatchmakingService;

class ChatWithGirlCommand
{
    public function __construct(
        private UserRegistrationService $registrationService,
        private MatchmakingService $matchmaking,
    ) {}

    public function execute(PlatformEnum $platform, string $platformUserId, string $username): string
    {
        $user = $this->registrationService->registerUser($platform, $platformUserId, $username);

        $this->matchmaking->enterQueue($user, 'girl');

        return 'در حال جستجو برای چت با دختر...';
    }
}
