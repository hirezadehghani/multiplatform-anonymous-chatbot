<?php

namespace App\Services\Chat;

use App\Models\User;
use App\Services\Referral\ReferralService;

class InviteCommand
{
    public function __construct(private ReferralService $referralService)
    {
    }

    public function handle(User $user, string $input): string
    {
        $key = trim($input);

        if ($key === '' || $key === '/invite' || mb_strtolower($key) === mb_strtolower('دعوت از دوستان')) {
            $linkCode = $this->referralService->generateInviteCode($user);
            $base = config('app.url', 'https://example.com');
            $url = rtrim($base, '/') . '/?ref=' . $linkCode->code;
            return "لینک دعوت شما: {$url}";
        }

        return 'دستور نامعتبر. برای دریافت لینک دعوت /invite را ارسال کنید.';
    }
}
