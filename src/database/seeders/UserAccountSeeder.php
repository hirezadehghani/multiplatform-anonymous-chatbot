<?php

namespace Database\Seeders;

use App\Enums\PlatformEnum;
use App\Models\Bot;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()
            ->where('email', 'admin@test.com')
            ->first();

        if ($admin === null) {
            $this->command?->warn('Admin user not found. Run AdminUserSeeder first.');

            return;
        }

        $bot = Bot::query()
            ->where('platform', PlatformEnum::WEB->value)
            ->where('name', 'Web Admin')
            ->first();

        if ($bot === null) {
            $this->command?->warn('Web admin bot not found. Run BotSeeder first.');

            return;
        }

        UserAccount::updateOrCreate(
            [
                'platform' => PlatformEnum::WEB->value,
                'platform_user_id' => (string) $admin->id,
            ],
            [
                'user_id' => $admin->id,
                'bot_id' => $bot->id,
                'username' => 'admin',
                'is_primary' => true,
            ]
        );
    }
}
