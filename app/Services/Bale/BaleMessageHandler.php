<?php

namespace App\Services\Bale;

use App\DTOs\Bale\BaleUpdate;
use App\Models\Bot;
use App\Models\UserAccount;
use App\Services\Platform\UserAccountResolver;

class BaleMessageHandler
{
    public function __construct(
        private readonly UserAccountResolver $userAccountResolver,
    ) {}

    public function handle(Bot $bot, BaleUpdate $update): void
    {
        if (! $update->hasUserInteraction()) {
            return;
        }

        $this->resolveUserAccount($bot, $update);

        // Command routing will be implemented in a later phase.
    }

    private function resolveUserAccount(Bot $bot, BaleUpdate $update): UserAccount
    {
        return $this->userAccountResolver->resolveOrCreate(
            bot: $bot,
            platformUserId: $update->platformUserId(),
            username: $update->username(),
            displayName: $update->displayName(),
        );
    }
}
