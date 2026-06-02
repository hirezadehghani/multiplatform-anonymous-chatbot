<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case DEPOSIT = 'deposit';

    case WITHDRAW = 'withdraw';

    case REWARD = 'reward';

    case PURCHASE = 'purchase';
}