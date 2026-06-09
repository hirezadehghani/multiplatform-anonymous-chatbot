<?php

namespace App\Services\Chat;

use App\Enums\BotCommand;

final class BotCommandRoute
{
    public function __construct(
        public BotCommand $command,
        public string $label,
        public string $rawCommand,
    ) {
    }
}
