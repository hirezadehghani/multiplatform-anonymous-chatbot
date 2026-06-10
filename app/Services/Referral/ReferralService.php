<?php

namespace App\Services\Referral;

use App\Models\User;
use App\Models\Referral;
use App\Models\LinkCode;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Str;

class ReferralService
{
    public function __construct(private ?WalletService $walletService = null)
    {
    }

    public function generateInviteCode(User $user): LinkCode
    {
        $code = Str::upper(Str::random(8));
        $ttlDays = config('referral.link_ttl_days', 30);
        return LinkCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addDays($ttlDays),
        ]);
    }

    public function handleReferralCode(User $newUser, string $referralCode): ?Referral
    {
        $linkCode = LinkCode::where('code', $referralCode)
            ->where(function ($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->first();

        if (! $linkCode) {
            return null;
        }

        // Prevent self referral
        if ($linkCode->user_id === $newUser->id) {
            return null;
        }

        // Prevent duplicate referrals for the same user
        if (Referral::where('referred_user_id', $newUser->id)->exists()) {
            return null;
        }

        $referrerId = $linkCode->user_id;

        $referrerReward = (int) config('referral.reward_referrer', 100);
        $referredReward = (int) config('referral.reward_referred', 50);

        $referral = Referral::create([
            'referrer_id' => $referrerId,
            'referred_user_id' => $newUser->id,
            'reward' => $referrerReward,
        ]);

        // Credit wallets if wallet service available
        if ($this->walletService) {
            // credit referrer
            try {
                $this->walletService->credit($linkCode->user, $referrerReward, 'Referral reward (referrer)');
            } catch (\Throwable $e) {
                // swallow; referral record still stands
            }

            // credit referred user
            try {
                $this->walletService->credit($newUser, $referredReward, 'Referral reward (referred)');
            } catch (\Throwable $e) {
                // swallow
            }
        }

        return $referral;
    }

    public function rewardReferrer(int $referrerId): void
    {
        $amount = (int) config('referral.reward_referrer', 100);
        if (! $this->walletService) {
            return;
        }
        $user = User::find($referrerId);
        if ($user) {
            $this->walletService->credit($user, $amount, 'Referral reward');
        }
    }
}
