<?php

use Illuminate\Support\Facades\Route;
use App\Connectors\BaleConnector;

Route::get('/', function () {
    return 'Application working! Written By Reza Dehghani contact with me at -> https://hireza.ir'
});


Route::get('/test-bale', function (BaleConnector $connector) {

    $connector->sendMessage(
        '12',
        'Hello from Laravel'
    );

    return 'Message sent';
});