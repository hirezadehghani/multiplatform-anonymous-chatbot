<?php

namespace App\Enums;

use HasOptions;

enum UserStatusEnum: string
{
    use HasOptions;

    case OFFLINE = 'offline';

    case SEARCHING = 'searching';

    case CHATTING = 'chatting';
}