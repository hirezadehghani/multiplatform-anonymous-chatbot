<?php

namespace App\Services\Chat;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

final class ProfileStateService
{
    // Simple conversation state stored in cache per user
    public function getState(User $user): array
    {
        return Cache::get($this->key($user), [
            'step' => 'start',
            'data' => [],
        ]);
    }

    public function setState(User $user, array $state): void
    {
        Cache::put($this->key($user), $state, now()->addHours(6));
    }

    public function clearState(User $user): void
    {
        Cache::forget($this->key($user));
    }

    private function key(User $user): string
    {
        return "profile_state:user:{$user->id}";
    }
}
