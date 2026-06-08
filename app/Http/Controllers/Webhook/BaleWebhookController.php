<?php

namespace App\Http\Controllers\Webhook;

use App\DTOs\Bale\BaleUpdate;
use App\Enums\PlatformEnum;
use App\Http\Controllers\Controller;
use App\Models\Bot;
use App\Services\Bale\BaleMessageHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BaleWebhookController extends Controller
{
    public function __construct(
        private readonly BaleMessageHandler $messageHandler,
    ) {}

    public function __invoke(Request $request, Bot $bot): JsonResponse
    {
        if ($bot->platform !== PlatformEnum::BALE->value || ! $bot->is_active) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $update = BaleUpdate::fromArray($request->all());

        $this->messageHandler->handle($bot, $update);

        return response()->json(['ok' => true]);
    }
}
