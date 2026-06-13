<?php

namespace App\Connectors;

use App\Contracts\PlatformConnector;
use App\Enums\PlatformEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class BaleConnector implements PlatformConnector
{
    public function __construct(
        private readonly string $token,
    ) {}

    public function sendMessage(
        string $platformUserId,
        string $message
    ): void {
        $url = sprintf(
            'https://tapi.bale.ai/bot%s/sendMessage',
            $this->token
        );

        $payload = [
            'chat_id' => $platformUserId,
            'text' => $message,
        ];

        Log::info('Bale sendMessage request', $payload);

        $response = Http::timeout(15)
            ->acceptJson()
            ->post($url, $payload);

        Log::info('Bale sendMessage response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Bale API error: ' . $response->body()
            );
        }
    }

    public function getPlatform(): PlatformEnum
    {
        return PlatformEnum::BALE;
    }
}
