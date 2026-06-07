<?php

namespace Database\Seeders;

use App\Enums\PlatformEnum;
use App\Models\Bot;
use Illuminate\Database\Seeder;

class BotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bot::updateOrCreate(
            [
                'platform' => PlatformEnum::WEB->value,
                'name' => 'Web Admin',
            ],
            [
                'token' => 'web-admin-seed-token',
                'is_active' => true,
                'settings' => null,
            ]
        );
    }
}
