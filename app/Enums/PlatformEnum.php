<?php

namespace App\Enums;

use App\Enums\Traits\HasOptions;

enum PlatformEnum: string
{
    use HasOptions;

    case BALE = 'bale';

    case TELEGRAM = 'telegram';

    case RUBIKA = 'rubika';

    case WEB = 'web';
}
