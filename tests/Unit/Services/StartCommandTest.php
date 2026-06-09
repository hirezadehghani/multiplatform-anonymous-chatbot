<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\LinkCode;
use App\Services\Chat\UserRegistrationService;
use App\Services\Chat\StartCommand;
use App\Services\Referral\ReferralService;
use App\Enums\PlatformEnum;
use Tests\TestCase;

class StartCommandTest extends TestCase
{
    private StartCommand $command;
    private UserRegistrationService $registrationService;
    private ReferralService $referralService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationService = new UserRegistrationService();
        $this->referralService = new ReferralService();
        $this->command = new StartCommand($this->registrationService, $this->referralService);
    }

    public function test_executes_and_returns_welcome_message(): void
    {
        $message = $this->command->execute(
            PlatformEnum::BALE,
            'bale_123',
            'test_user'
        );

        $this->assertNotEmpty($message);
        $this->assertStringContainsString('خوش آمدید', $message);
    }

    public function test_creates_user_during_execution(): void
    {
        $this->assertDatabaseCount('users', 0);

        $this->command->execute(
            PlatformEnum::BALE,
            'bale_123',
            'test_user'
        );

        $this->assertDatabaseCount('users', 1);
    }

    public function test_creates_wallet_during_execution(): void
    {
        $this->assertDatabaseCount('wallets', 0);

        $this->command->execute(
            PlatformEnum::BALE,
            'bale_123',
            'test_user'
        );

        $this->assertDatabaseCount('wallets', 1);
    }

    public function test_creates_user_account_during_execution(): void
    {
        $this->assertDatabaseCount('user_accounts', 0);

        $this->command->execute(
            PlatformEnum::BALE,
            'bale_123',
            'test_user'
        );

        $this->assertDatabaseCount('user_accounts', 1);
    }

    public function test_handles_referral_code_if_provided(): void
    {
        // Create a referrer
        $referrer = User::factory()->create();
        LinkCode::create([
            'user_id' => $referrer->id,
            'code' => 'REF123',
            'expires_at' => now()->addDays(30),
        ]);

        $this->assertDatabaseCount('referrals', 0);

        $this->command->execute(
            PlatformEnum::BALE,
            'bale_123',
            'test_user',
            'REF123'
        );

        $this->assertDatabaseCount('referrals', 1);
        $this->assertDatabaseHas('referrals', [
            'referrer_id' => $referrer->id,
        ]);
    }

    public function test_ignores_invalid_referral_code(): void
    {
        $this->assertDatabaseCount('referrals', 0);

        $message = $this->command->execute(
            PlatformEnum::BALE,
            'bale_123',
            'test_user',
            'INVALID'
        );

        $this->assertDatabaseCount('referrals', 0);
        $this->assertNotEmpty($message);
    }

    public function test_is_platform_independent(): void
    {
        $baleMessage = $this->command->execute(
            PlatformEnum::BALE,
            'bale_123',
            'bale_user'
        );

        $telegramMessage = $this->command->execute(
            PlatformEnum::TELEGRAM,
            'telegram_456',
            'telegram_user'
        );

        $this->assertEquals($baleMessage, $telegramMessage);
        $this->assertEquals(2, User::count());
    }
}
