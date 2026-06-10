<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\Chat\ProfileService;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    public function test_sets_profile_fields_and_gives_reward_once(): void
    {
        $user = User::factory()->create(['profile_completed' => false]);

        $service = new ProfileService();

        $service->setGender($user, 'female');
        $this->assertEquals('female', $user->fresh()->gender);

        $service->setAge($user, 30);
        $this->assertEquals(30, $user->fresh()->age);

        $service->setCity($user, 'Tehran');
        $this->assertEquals('Tehran', $user->fresh()->city);

        $this->assertDatabaseCount('wallets', 0);

        $service->completeProfile($user);

        $this->assertTrue($user->fresh()->profile_completed);
        $this->assertDatabaseCount('wallets', 1);

        $balance = $user->wallet->balance;
        $this->assertGreaterThanOrEqual(0, $balance);

        // Calling again should not double reward
        $service->completeProfile($user);
        $this->assertEquals($user->fresh()->wallet->balance, $balance);
    }
}
