<?php

namespace App\Services\Chat;

use App\Models\User;
use App\Models\Wallet;

class ProfileService
{
    public function setGender(User $user, string $gender): void
    {
        $user->gender = $gender;
        $user->save();
    }

    public function setAge(User $user, int $age): void
    {
        $user->age = $age;
        $user->save();
    }

    public function setCity(User $user, string $city): void
    {
        $user->city = $city;
        $user->save();
    }

    public function completeProfile(User $user): void
    {
        if ($user->profile_completed) {
            return;
        }

        $user->profile_completed = true;
        $user->save();

        // Give one-time reward — create wallet if missing
        $wallet = $user->wallet()->first();
        if (!$wallet) {
            Wallet::create(['user_id' => $user->id, 'balance' => 0]);
            $wallet = $user->wallet()->first();
        }

        // reward amount from config (default 100)
        $amount = config('app.profile_completion_reward', 100);
        $wallet->balance += $amount;
        $wallet->save();
    }
}
