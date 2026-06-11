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

    public function label(): string
    {
        return match ($this) {
            self::BALE => 'Bale',
            self::TELEGRAM => 'Telegram',
            self::RUBIKA => 'Rubika',
            self::WEB => 'Web',
        };
    }
}
