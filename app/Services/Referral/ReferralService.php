<?php

namespace App\Services\Referral;

use App\Models\User;
use App\Models\Referral;
use App\Models\LinkCode;

class ReferralService
{
    public function handleReferralCode(User $newUser, string $referralCode): ?Referral
    {
        // Find the referrer by link code
        $linkCode = LinkCode::where('code', $referralCode)->first();

        if (!$linkCode) {
            return null;
        }

        // Create referral record
        $referral = Referral::create([
            'referrer_id' => $linkCode->user_id,
            'referred_user_id' => $newUser->id,
            'reward' => 0, // Reward amount can be set via config
        ]);

        // Reward the referrer
        $this->rewardReferrer($linkCode->user_id);

        return $referral;
    }

    public function rewardReferrer(int $referrerId): void
    {
        // TODO: Implement reward logic (update wallet, give bonus credits, etc.)
    }
}
