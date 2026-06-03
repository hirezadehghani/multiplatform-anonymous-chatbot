<?php

namespace App\Enums;

use HasOptions;

enum MessageTypeEnum: string
{

    use HasOptions;

    case TEXT = 'text';

    case IMAGE = 'image';

    case VIDEO = 'video';

    case VOICE = 'voice';

    case FILE = 'file';
}