<?php

namespace App\Services\Chat;

use App\Models\User;
use App\Enums\PlatformEnum;
use App\Services\Referral\ReferralService;

class StartCommand
{
    public function __construct(
        private UserRegistrationService $userRegistrationService,
        private ReferralService $referralService,
    ) {
    }

    public function execute(
        PlatformEnum $platform,
        string $platformUserId,
        string $username,
        ?string $referralCode = null,
    ): string {
        // Register user and get instance
        $user = $this->userRegistrationService->registerUser(
            $platform,
            $platformUserId,
            $username,
        );

        // Handle referral code if provided
        if ($referralCode) {
            $this->referralService->handleReferralCode($user, $referralCode);
        }

        return $this->getWelcomeMessage($user);
    }

    private function getWelcomeMessage(User $user): string
    {
        return "خوش آمدید! 👋\n\nبه پلتفرم چت ناشناس خوش آمدید. برای شروع، یکی از گزینه‌های زیر را انتخاب کنید.";
    }
}
