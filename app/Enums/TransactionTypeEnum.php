<?php

namespace App\Enums;

use HasOptions;

enum TransactionTypeEnum: string
{
    use HasOptions;
    
    case DEPOSIT = 'deposit';

    case WITHDRAW = 'withdraw';

    case REWARD = 'reward';

    case PURCHASE = 'purchase';
}