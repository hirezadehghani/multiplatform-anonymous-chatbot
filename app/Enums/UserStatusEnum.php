<?php

namespace App\Enums;

enum UserStatusEnum: string
{
    case OFFLINE = 'offline';

    case SEARCHING = 'searching';

    case CHATTING = 'chatting';
}