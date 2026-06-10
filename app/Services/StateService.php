<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\User;
use App\Models\UserState;
use Illuminate\Support\Arr;

class StateService
{
    /**
     * Store or update the user's current state and optional payload.
     */
    public function setState(User $user, Bot $bot, string $state, array $payload = []): UserState
    {
        return UserState::updateOrCreate(
            [
                'user_id' => $user->id,
                'bot_id' => $bot->id,
            ],
            [
                'state' => $state,
                'payload' => $payload ?: null,
            ]
        );
    }

    /**
     * Get the current state record for a user and bot.
     */
    public function getState(User $user, Bot $bot): ?UserState
    {
        return UserState::where('user_id', $user->id)
            ->where('bot_id', $bot->id)
            ->first();
    }

    /**
     * Clear the stored state for user+bot.
     */
    public function clearState(User $user, Bot $bot): void
    {
        UserState::where('user_id', $user->id)
            ->where('bot_id', $bot->id)
            ->delete();
    }

    /**
     * Resume the state: returns an array with state and payload or null.
     */
    public function resumeState(User $user, Bot $bot): ?array
    {
        $record = $this->getState($user, $bot);
        if (! $record) {
            return null;
        }

        return [
            'state' => $record->state,
            'payload' => $record->payload ?? [],
            'record' => $record,
        ];
    }

    /**
     * Merge additional payload into existing state payload.
     */
    public function mergePayload(User $user, Bot $bot, array $payload): ?UserState
    {
        $record = $this->getState($user, $bot);
        if (! $record) {
            return null;
        }

        $new = array_merge(Arr::wrap($record->payload ?? []), $payload);
        $record->payload = $new;
        $record->save();

        return $record;
    }
}
