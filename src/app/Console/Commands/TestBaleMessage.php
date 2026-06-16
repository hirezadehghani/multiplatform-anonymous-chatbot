<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Connectors\BaleConnector;

class TestBaleMessage extends Command
{
    protected $signature = 'bale:test {chatId}';

    protected $description = 'Send test message to Bale';

    public function handle(BaleConnector $connector): int
    {
        $connector->sendMessage(
            $this->argument('chatId'),
            'Hello from Laravel'
        );

        $this->info('Message sent.');

        return self::SUCCESS;
    }
}