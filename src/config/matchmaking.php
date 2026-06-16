<?php

return [

    'redis_connection' => env('MATCHMAKING_REDIS_CONNECTION', 'default'),

    'queue_key' => env('MATCHMAKING_QUEUE_KEY', 'matchmaking:queue'),

    'waiting_key' => env('MATCHMAKING_WAITING_KEY', 'matchmaking:waiting'),

];
