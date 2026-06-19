<?php

namespace App\Services\Wallet;

use App\Enums\TransactionTypeEnum;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletService
{
    public function __construct(private TransactionService $transactions)
    {
    }

    public function getBalance(User $user): int
    {
        $wallet = $this->ensureWallet($user);
        return (int) $wallet->balance;
    }

    public function credit(User $user, int $amount, ?string $description = null): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive.');
        }

        DB::transaction(function () use ($user, $amount, $description): void {
            $wallet = $this->ensureWallet($user);
            $this->transactions->create($user, $amount, TransactionTypeEnum::DEPOSIT, $description);
            $wallet->balance += $amount;
            $wallet->save();
        });
    }

    public function debit(User $user, int $amount, ?string $description = null): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive.');
        }

        DB::transaction(function () use ($user, $amount, $description): void {
            $wallet = $this->ensureWallet($user);
            if ($wallet->balance < $amount) {
                throw new InvalidArgumentException('Insufficient funds.');
            }
            $this->transactions->create($user, -$amount, TransactionTypeEnum::WITHDRAW, $description);
            $wallet->balance -= $amount;
            $wallet->save();
        });
    }

    public function transactions(User $user, int $limit = 50)
    {
        return $user->transactions()->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    private function ensureWallet(User $user): Wallet
    {
        $wallet = $user->wallet()->first();
        if (! $wallet) {
            return Wallet::query()->create(['user_id' => $user->id, 'balance' => 0]);
        }
        return $wallet;
    }
}
