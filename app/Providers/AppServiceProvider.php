<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Connectors\BaleConnector;
use App\Connectors\TelegramConnector;
use App\Connectors\RubikaConnector;
use App\Contracts\PlatformConnector;
use App\Services\Chat\MessageRelayService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register platform connectors and tag them so we can resolve an iterable of connectors.
        $this->app->singleton(BaleConnector::class, function ($app) {
            $token = config('services.bale.token') ?? '';
            return new BaleConnector($token);
        });

        $this->app->singleton(TelegramConnector::class, function ($app) {
            $token = config('services.telegram.token') ?? '';
            return new TelegramConnector($token);
        });

        $this->app->singleton(RubikaConnector::class, function ($app) {
            $token = config('services.rubika.token') ?? '';
            return new RubikaConnector($token);
        });

        $this->app->tag([
            BaleConnector::class,
            TelegramConnector::class,
            RubikaConnector::class,
        ], 'platform.connectors');

        // Bind MessageRelayService using tagged connectors so constructor receives iterable.
        $this->app->singleton(MessageRelayService::class, function ($app) {
            $connectors = $app->tagged('platform.connectors');

            return new MessageRelayService(
                $connectors,
                $app->make(\App\Services\Chat\ChatRoomService::class),
                $app->make(\App\Services\Chat\MessageService::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
