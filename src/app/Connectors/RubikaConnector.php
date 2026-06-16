<?php

namespace App\Connectors;

use App\Contracts\PlatformConnector;
use App\Enums\PlatformEnum;

final class RubikaConnector implements PlatformConnector
{
    public function __construct(
        private readonly string $token,
    ) {}

    public function sendMessage(string $platformUserId, string $message): void
    {
        //
    }

    public function getPlatform(): PlatformEnum
    {
        return PlatformEnum::RUBIKA;
    }
}
