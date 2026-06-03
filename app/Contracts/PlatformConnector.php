<?php 

interface PlatformConnector
{
    public function sendMessage(
        string $platformUserId,
        string $message
    );

    public function getPlatform(): string;
}

?>