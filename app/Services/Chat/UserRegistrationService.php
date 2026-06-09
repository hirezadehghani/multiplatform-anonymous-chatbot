<?php

namespace App\Services\Chat;

use App\Models\User;
use App\Models\UserAccount;
use App\Models\Wallet;
use App\Enums\PlatformEnum;
use Illuminate\Database\Eloquent\Model;

class UserRegistrationService
{
    public function registerUser(
        PlatformEnum $platform,
        string $platformUserId,
        string $username,
    ): User {
        // Create or get user (per platform account)
        $userAccount = UserAccount::where('platform', $platform)
            ->where('platform_user_id', $platformUserId)
            ->with('user')
            ->first();

        if ($userAccount && $userAccount->user) {
            return $userAccount->user;
        }

        // Create new user if doesn't exist
        $user = User::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'status' => 'offline',
        ]);

        // Create wallet
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        // Create user account
        UserAccount::create([
            'user_id' => $user->id,
            'platform' => $platform,
            'platform_user_id' => $platformUserId,
            'username' => $username,
            'is_primary' => true,
        ]);

        return $user;
    }
}
