<?php

namespace App\Enums;

enum ChatRoomStatusEnum: string
{
    case ACTIVE = 'active';

    case ENDED = 'ended';
}
