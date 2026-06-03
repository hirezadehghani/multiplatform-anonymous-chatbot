<?php

namespace App\Enums;

use HasOptions;

enum ChatRoomStatusEnum: string
{
    use HasOptions;
    
    case ACTIVE = 'active';

    case ENDED = 'ended';
}