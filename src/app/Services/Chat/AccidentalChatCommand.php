<?php

namespace App\Services\Chat;

use App\Enums\PlatformEnum;
use App\Models\User;
use App\Services\Chat\UserRegistrationService;
use App\Services\Matchmaking\MatchmakingService;

class AccidentalChatCommand
{
    public function __construct(
        private UserRegistrationService $registrationService,
        private MatchmakingService $matchmaking,
    ) {}

    public function execute(PlatformEnum $platform, string $platformUserId, string $username): string
    {
        $user = $this->registrationService->registerUser($platform, $platformUserId, $username);

        $this->matchmaking->enterQueue($user, 'any');

        return 'در حال جستجو برای شریک چت...';
    }
}
