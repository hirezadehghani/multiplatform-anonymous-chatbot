<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\UserAccount;
use App\Models\Wallet;
use App\Services\Chat\UserRegistrationService;
use App\Enums\PlatformEnum;
use Tests\TestCase;

class UserRegistrationServiceTest extends TestCase
{
    private UserRegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserRegistrationService();
    }

    public function test_registers_new_user_with_all_required_entities(): void
    {
        $user = $this->service->registerUser(
            PlatformEnum::BALE,
            'bale_123',
            'test_user'
        );

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->uuid);
        $this->assertEquals('offline', $user->status);

        // Verify wallet created
        $this->assertNotNull($user->wallet);
        $this->assertEquals(0, $user->wallet->balance);

        // Verify user account created
        $this->assertEquals(1, $user->accounts()->count());
        $this->assertEquals(PlatformEnum::BALE, $user->accounts()->first()->platform);
        $this->assertEquals('bale_123', $user->accounts()->first()->platform_user_id);
        $this->assertEquals('test_user', $user->accounts()->first()->username);
        $this->assertTrue($user->accounts()->first()->is_primary);
    }

    public function test_returns_existing_user_if_already_registered(): void
    {
        $user1 = $this->service->registerUser(
            PlatformEnum::BALE,
            'bale_123',
            'test_user'
        );

        $user2 = $this->service->registerUser(
            PlatformEnum::BALE,
            'bale_123',
            'test_user'
        );

        $this->assertEquals($user1->id, $user2->id);
        $this->assertEquals(1, User::count());
    }

    public function test_creates_separate_users_for_different_platforms(): void
    {
        $user1 = $this->service->registerUser(
            PlatformEnum::BALE,
            'bale_123',
            'bale_user'
        );

        $user2 = $this->service->registerUser(
            PlatformEnum::TELEGRAM,
            'telegram_123',
            'telegram_user'
        );

        $this->assertNotEquals($user1->id, $user2->id);
        $this->assertEquals(2, User::count());
    }

    public function test_creates_separate_users_for_same_platform_different_ids(): void
    {
        $user1 = $this->service->registerUser(
            PlatformEnum::BALE,
            'bale_123',
            'user1'
        );

        $user2 = $this->service->registerUser(
            PlatformEnum::BALE,
            'bale_456',
            'user2'
        );

        $this->assertNotEquals($user1->id, $user2->id);
        $this->assertEquals(2, User::count());
    }
}
