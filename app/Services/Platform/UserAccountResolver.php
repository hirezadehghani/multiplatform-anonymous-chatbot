<?php

namespace App\Services\Platform;

use App\Enums\PlatformEnum;
use App\Models\Bot;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAccountResolver
{
    public function resolveOrCreate(
        Bot $bot,
        string $platformUserId,
        ?string $username = null,
        ?string $displayName = null,
    ): UserAccount {
        $platform = PlatformEnum::from($bot->platform);

        $account = UserAccount::query()
            ->where('platform', $platform)
            ->where('platform_user_id', $platformUserId)
            ->first();

        if ($account !== null) {
            $this->syncAccountMetadata($account, $username, $displayName);

            return $account;
        }

        return DB::transaction(function () use ($bot, $platform, $platformUserId, $username, $displayName): UserAccount {
            $user = User::query()->create([
                'uuid' => (string) Str::uuid(),
                'display_name' => $displayName,
                'last_seen_at' => now(),
            ]);

            Wallet::query()->create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            return UserAccount::query()->create([
                'user_id' => $user->id,
                'bot_id' => $bot->id,
                'platform' => $platform,
                'platform_user_id' => $platformUserId,
                'username' => $username,
                'is_primary' => true,
            ]);
        });
    }

    private function syncAccountMetadata(
        UserAccount $account,
        ?string $username,
        ?string $displayName,
    ): void {
        $updates = [];

        if ($username !== null && $account->username !== $username) {
            $updates['username'] = $username;
        }

        if ($updates !== []) {
            $account->update($updates);
        }

        $userUpdates = [
            'last_seen_at' => now(),
        ];

        if ($displayName !== null && $account->user->display_name === null) {
            $userUpdates['display_name'] = $displayName;
        }

        $account->user->update($userUpdates);
    }
}
