<?php

use Illuminate\Support\Facades\Route;
use App\Connectors\BaleConnector;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-bale', function (BaleConnector $connector) {

    $connector->sendMessage(
        '12',
        'Hello from Laravel'
    );

    return 'Message sent';
});