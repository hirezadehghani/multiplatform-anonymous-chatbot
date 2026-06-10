<?php

use App\Models\Bot;
use App\Models\User;
use App\Models\UserState;
use App\Services\StateService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('stores and retrieves a user state with payload', function () {
    $user = User::factory()->create();
    $bot = Bot::factory()->create();

    $service = new StateService();

    $payload = ['step' => 1, 'data' => ['name' => 'alice']];

    $service->setState($user, $bot, 'onboarding', $payload);

    $record = $service->getState($user, $bot);

    expect($record)->toBeInstanceOf(UserState::class);
    expect($record->state)->toBe('onboarding');
    expect($record->payload)->toMatchArray($payload);
});

it('resumes and clears state', function () {
    $user = User::factory()->create();
    $bot = Bot::factory()->create();

    $service = new StateService();

    $service->setState($user, $bot, 'form', ['field' => 'value']);

    $resumed = $service->resumeState($user, $bot);

    expect($resumed)->not->toBeNull();
    expect($resumed['state'])->toBe('form');
    expect($resumed['payload'])->toHaveKey('field');

    $service->clearState($user, $bot);

    $after = $service->getState($user, $bot);
    expect($after)->toBeNull();
});

it('merges payload into existing state', function () {
    $user = User::factory()->create();
    $bot = Bot::factory()->create();

    $service = new StateService();

    $service->setState($user, $bot, 'multi', ['a' => 1]);

    $service->mergePayload($user, $bot, ['b' => 2]);

    $record = $service->getState($user, $bot);
    expect($record->payload)->toMatchArray(['a' => 1, 'b' => 2]);
});
