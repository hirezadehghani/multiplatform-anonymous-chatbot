<?php

namespace Tests\Unit\Services;

use App\Enums\PlatformEnum;
use App\Services\Chat\AccidentalChatCommand;
use App\Services\Chat\ChatWithBoyCommand;
use App\Services\Chat\ChatWithGirlCommand;
use App\Services\Chat\ChatWithGoalCommand;
use App\Services\Chat\UserRegistrationService;
use App\Services\Matchmaking\MatchmakingService;
use App\Models\User;
use Tests\TestCase;

class MatchCommandsTest extends TestCase
{
    public function test_accidental_chat_calls_matchmaking_enter_queue(): void
    {
        $registration = new UserRegistrationService();

        $matchMock = $this->createMock(MatchmakingService::class);
        $matchMock->expects($this->once())
            ->method('enterQueue')
            ->with($this->callback(function ($user) {
                return $user instanceof User;
            }), 'any');

        $cmd = new AccidentalChatCommand($registration, $matchMock);

        $msg = $cmd->execute(PlatformEnum::BALE, 'bale_1', 'tester');

        $this->assertStringContainsString('جستجو', $msg);
    }

    public function test_chat_with_girl_calls_matchmaking_with_girl_mode(): void
    {
        $registration = new UserRegistrationService();

        $matchMock = $this->createMock(MatchmakingService::class);
        $matchMock->expects($this->once())
            ->method('enterQueue')
            ->with($this->isInstanceOf(User::class), 'girl');

        $cmd = new ChatWithGirlCommand($registration, $matchMock);
        $cmd->execute(PlatformEnum::BALE, 'bale_2', 'tester');
    }

    public function test_chat_with_boy_calls_matchmaking_with_boy_mode(): void
    {
        $registration = new UserRegistrationService();

        $matchMock = $this->createMock(MatchmakingService::class);
        $matchMock->expects($this->once())
            ->method('enterQueue')
            ->with($this->isInstanceOf(User::class), 'boy');

        $cmd = new ChatWithBoyCommand($registration, $matchMock);
        $cmd->execute(PlatformEnum::BALE, 'bale_3', 'tester');
    }

    public function test_chat_with_goal_calls_matchmaking_with_goal_mode(): void
    {
        $registration = new UserRegistrationService();

        $matchMock = $this->createMock(MatchmakingService::class);
        $matchMock->expects($this->once())
            ->method('enterQueue')
            ->with($this->isInstanceOf(User::class), 'goal');

        $cmd = new ChatWithGoalCommand($registration, $matchMock);
        $cmd->execute(PlatformEnum::BALE, 'bale_4', 'tester');
    }
}
