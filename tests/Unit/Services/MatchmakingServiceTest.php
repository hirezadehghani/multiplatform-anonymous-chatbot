<?php

namespace Tests\Unit\Services;

use App\Enums\UserStatusEnum;
use App\Models\User;
use App\Services\Chat\ChatService;
use App\Services\Matchmaking\MatchmakingService;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Redis\Connections\Connection;
use Tests\TestCase;

class MatchmakingServiceTest extends TestCase
{
    private MatchmakingService $service;
    private $redisFactoryMock;
    private $connectionMock;

    protected function setUp(): void
    {
        parent::setUp();

        $chatService = $this->createMock(ChatService::class);

        $this->connectionMock = new class {
            public array $calls = [];

            public function sismember($k, $v)
            {
                $this->calls[] = ['sismember', $k, $v];
                return false;
            }

            public function pipeline($closure)
            {
                $this->calls[] = ['pipeline_start'];
                $closure($this);
                $this->calls[] = ['pipeline_end'];
            }

            public function lpush($k, $v)
            {
                $this->calls[] = ['lpush', $k, $v];
            }

            public function sadd($k, $v)
            {
                $this->calls[] = ['sadd', $k, $v];
            }

            public function lrem($k, $c, $v)
            {
                $this->calls[] = ['lrem', $k, $c, $v];
            }

            public function srem($k, $v)
            {
                $this->calls[] = ['srem', $k, $v];
            }

            public function eval($script, $num, $queue, $userId)
            {
                $this->calls[] = ['eval', $queue, $userId];
                return null;
            }
        };

        $this->redisFactoryMock = $this->createMock(RedisFactory::class);
        $this->redisFactoryMock->method('connection')->willReturn($this->connectionMock);

        $this->service = new MatchmakingService($chatService, $this->redisFactoryMock);
    }

    public function test_enter_queue_adds_user_and_sets_searching_status(): void
    {
        $user = User::factory()->create(['status' => UserStatusEnum::OFFLINE]);

        $this->service->enterQueue($user, 'any');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => UserStatusEnum::SEARCHING,
        ]);

        $this->assertTrue(collect($this->connectionMock->calls)->contains(function ($c) {
            return $c[0] === 'lpush';
        }));

        $this->assertTrue(collect($this->connectionMock->calls)->contains(function ($c) {
            return $c[0] === 'sadd';
        }));
    }

    public function test_leave_queue_removes_user_and_sets_offline_status(): void
    {
        $user = User::factory()->create(['status' => UserStatusEnum::SEARCHING]);

        $this->service->leaveQueue($user, 'any');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => UserStatusEnum::OFFLINE,
        ]);

        $this->assertTrue(collect($this->connectionMock->calls)->contains(function ($c) {
            return $c[0] === 'lrem';
        }));

        $this->assertTrue(collect($this->connectionMock->calls)->contains(function ($c) {
            return $c[0] === 'srem';
        }));
    }

    public function test_find_partner_marks_user_waiting_when_no_partner(): void
    {
        $user = User::factory()->create(['status' => UserStatusEnum::OFFLINE]);

        $this->service->enterQueue($user, 'any');

        $room = $this->service->findPartner($user, 'any');

        $this->assertNull($room);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => UserStatusEnum::SEARCHING,
        ]);

        $this->assertTrue(collect($this->connectionMock->calls)->contains(function ($c) {
            return $c[0] === 'eval';
        }));
    }
}
