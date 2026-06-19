<?php

use App\Services\Chat\MessageRelayService;

uses(Tests\TestCase::class);

it('resolves MessageRelayService from the container', function () {
    $service = app(MessageRelayService::class);

    expect($service)->toBeInstanceOf(MessageRelayService::class);
});
