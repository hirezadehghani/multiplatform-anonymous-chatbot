<?php

namespace Database\Seeders;

use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'uuid' => (string) Str::uuid(),
                'display_name' => 'Admin',
                'password' => Hash::make('password'),
                'status' => UserStatusEnum::OFFLINE,
            ]
        );

        Wallet::updateOrCreate(
            ['user_id' => $admin->id],
            ['balance' => 0]
        );
    }
}
