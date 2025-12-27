<?php

use App\Services\Neo4jService;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

app()->cors([
    'origin' => _env('FRONTEND_URL'),
    'methods' => ['GET', 'POST'],
]);

app()->post('/api/quest', 'App\Controllers\StageController@processAction');


    $service = new Neo4jService();



});

app()->run();
