<?php

namespace Tests\Unit;

use App\Enums\TransactionTypeEnum;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Wallet\TransactionService;
use App\Services\Wallet\WalletService;
use InvalidArgumentException;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    public function test_get_balance_creates_wallet()
    {
        $user = User::factory()->create();

        $txSvc = new TransactionService();
        $svc = new WalletService($txSvc);

        $balance = $svc->getBalance($user);

        $this->assertEquals(0, $balance);
        $this->assertDatabaseHas('wallets', ['user_id' => $user->id]);
    }

    public function test_credit_updates_balance_and_creates_transaction()
    {
        $user = User::factory()->create();
        Wallet::create(['user_id' => $user->id, 'balance' => 0]);

        $txSvc = new TransactionService();
        $svc = new WalletService($txSvc);

        $svc->credit($user, 200, 'buy coins');

        $this->assertDatabaseHas('wallets', ['user_id' => $user->id, 'balance' => 200]);
        $this->assertDatabaseHas('transactions', ['user_id' => $user->id, 'amount' => 200, 'description' => 'buy coins']);
    }

    public function test_debit_throws_on_insufficient_funds()
    {
        $user = User::factory()->create();
        Wallet::create(['user_id' => $user->id, 'balance' => 50]);

        $txSvc = new TransactionService();
        $svc = new WalletService($txSvc);

        $this->expectException(InvalidArgumentException::class);
        $svc->debit($user, 100, 'attempt withdraw');
    }
}
