<?php

namespace App\Services\Wallet;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Models\User;

class TransactionService
{
    /**
     * Create a new transaction for a user.
     */
    public function create(User $user, int $amount, TransactionTypeEnum $type, ?string $description = null): Transaction
    {
        return Transaction::query()->create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => $type->value,
            'description' => $description,
        ]);
    }
}
