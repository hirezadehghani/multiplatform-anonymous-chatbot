<?php

namespace App\Services\Chat;

use App\Models\User;
use App\Services\Wallet\WalletService;

class WalletCommand
{
    public function __construct(private WalletService $walletService)
    {
    }

    public function handle(User $user, string $input): string
    {
        $key = trim($input);

        // menu
        if ($key === '' || $key === '/wallet' || mb_strtolower($key) === mb_strtolower('کیف پول')) {
            return $this->menu();
        }

        if (mb_stripos($key, 'موجودی') !== false) {
            $balance = $this->walletService->getBalance($user);
            return "موجودی حساب شما: {$balance}";
        }

        if (mb_stripos($key, 'خرید سکه') !== false) {
            // For now, just show a placeholder that instructs how to purchase
            return 'برای خرید سکه لطفاً مبلغ را به تومان وارد کنید. مثال: خرید سکه 100';
        }

        if (mb_stripos($key, 'تاریخچه') !== false) {
            $txs = $this->walletService->transactions($user, 20);
            if ($txs->isEmpty()) {
                return 'هیچ تراکنشی وجود ندارد.';
            }
            $lines = [];
            foreach ($txs as $tx) {
                $lines[] = sprintf("%s: %d (%s)", $tx->created_at->toDateTimeString(), $tx->amount, $tx->description ?? '-');
            }
            return implode("\n", $lines);
        }

        // unknown
        return 'دستور نامعتبر. برای بازگشت به منو، /wallet را ارسال کنید.';
    }

    private function menu(): string
    {
        return implode("\n", [
            'کیف پول — گزینه ها:',
            'موجودی',
            'خرید سکه',
            'تاریخچه ترانکش ها',
        ]);
    }
}
