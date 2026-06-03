<?php

namespace App\Enums;

use HasOptions;

enum PlatformEnum: string
{

    use HasOptions;

    case BALE = 'bale';

    case TELEGRAM = 'telegram';

    case RUBIKA = 'rubika';

    case WEB = 'web';
}