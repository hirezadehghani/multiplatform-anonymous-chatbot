<?php

namespace Tests\Unit;

use App\Enums\TransactionTypeEnum;
use App\Models\User;
use App\Services\Wallet\TransactionService;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    public function test_create_records_transaction()
    {
        $user = User::factory()->create();

        $svc = new TransactionService();
        $tx = $svc->create($user, 150, TransactionTypeEnum::DEPOSIT, 'test deposit');

        $this->assertDatabaseHas('transactions', [
            'id' => $tx->id,
            'user_id' => $user->id,
            'amount' => 150,
            'type' => TransactionTypeEnum::DEPOSIT->value,
            'description' => 'test deposit',
        ]);
    }
}
