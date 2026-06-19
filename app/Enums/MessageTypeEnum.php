<?php

namespace App\Enums;

enum MessageTypeEnum: string
{
    case TEXT = 'text';

    case IMAGE = 'image';

    case VIDEO = 'video';

    case VOICE = 'voice';

    case FILE = 'file';
}