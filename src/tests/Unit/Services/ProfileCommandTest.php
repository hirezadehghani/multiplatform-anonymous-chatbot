<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\Chat\ProfileStateService;
use App\Services\Chat\ProfileService;
use App\Services\Chat\ProfileCommand;
use Tests\TestCase;

class ProfileCommandTest extends TestCase
{
    public function test_profile_flow_completes_and_rewards(): void
    {
        $user = User::factory()->create(['profile_completed' => false]);

        $state = new ProfileStateService();
        $service = new ProfileService();
        $command = new ProfileCommand($state, $service);

        $resp = $command->handle($user, '');
        $this->assertStringContainsString('جنسیت', $resp);

        $resp = $command->handle($user, 'female');
        $this->assertStringContainsString('سن', $resp);

        $resp = $command->handle($user, '25');
        $this->assertStringContainsString('شهر', $resp);

        $resp = $command->handle($user, 'Tehran');
        $this->assertStringContainsString('تکمیل', $resp);

        $this->assertTrue($user->fresh()->profile_completed);
        $this->assertDatabaseCount('wallets', 1);
    }
}
