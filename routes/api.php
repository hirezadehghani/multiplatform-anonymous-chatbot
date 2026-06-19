<?php

use App\Http\Controllers\Webhook\BaleWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')
    ->prefix('webhooks')
    ->group(function (): void {
        Route::post('bale/{bot}', BaleWebhookController::class)
            ->name('webhooks.bale');
    });
