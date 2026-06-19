<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\LinkCode;
use App\Models\Referral;
use App\Services\Referral\ReferralService;
use App\Services\Chat\InviteCommand;

class ReferralServiceTest extends TestCase
{
    public function test_generate_invite_creates_link_code(): void
    {
        $user = User::factory()->create();

        $svc = new ReferralService();
        $link = $svc->generateInviteCode($user);

        $this->assertDatabaseHas('link_codes', [
            'user_id' => $user->id,
            'code' => $link->code,
        ]);
    }

    public function test_handle_referral_creates_and_prevents_duplicates(): void
    {
        $referrer = User::factory()->create();
        LinkCode::create([
            'user_id' => $referrer->id,
            'code' => 'REFTEST',
            'expires_at' => now()->addDays(10),
        ]);

        $svc = new ReferralService();

        $newUser = User::factory()->create();
        $ref = $svc->handleReferralCode($newUser, 'REFTEST');

        $this->assertNotNull($ref);
        $this->assertDatabaseCount('referrals', 1);

        // Second attempt for same user should be ignored
        $ref2 = $svc->handleReferralCode($newUser, 'REFTEST');
        $this->assertNull($ref2);
        $this->assertDatabaseCount('referrals', 1);
    }

    public function test_invite_command_returns_link_and_creates_code(): void
    {
        $user = User::factory()->create();
        $svc = new ReferralService();
        $cmd = new InviteCommand($svc);

        $res = $cmd->handle($user, '/invite');

        $this->assertStringContainsString('لینک دعوت شما', $res);
        $this->assertDatabaseCount('link_codes', 1);
    }
}
