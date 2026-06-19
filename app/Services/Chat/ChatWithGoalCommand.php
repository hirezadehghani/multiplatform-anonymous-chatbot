<?php

namespace App\Services\Chat;

use App\Enums\PlatformEnum;
use App\Services\Chat\UserRegistrationService;
use App\Services\Matchmaking\MatchmakingService;

class ChatWithGoalCommand
{
    public function __construct(
        private UserRegistrationService $registrationService,
        private MatchmakingService $matchmaking,
    ) {}

    public function execute(PlatformEnum $platform, string $platformUserId, string $username): string
    {
        $user = $this->registrationService->registerUser($platform, $platformUserId, $username);

        $this->matchmaking->enterQueue($user, 'goal');

        return 'در حال جستجو برای چت هدفمند...';
    }
}
