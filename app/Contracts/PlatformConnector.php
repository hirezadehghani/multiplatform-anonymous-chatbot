<?php

namespace App\Contracts;

use App\Enums\PlatformEnum;

interface PlatformConnector
{
    /**
     * Send a text message to a user on the platform.
     */
    public function sendMessage(string $platformUserId, string $message): void;

    public function getPlatform(): PlatformEnum;
}
