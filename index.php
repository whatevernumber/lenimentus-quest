<?php

use Classes\Stage;
use Services\Neo4jService;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

app()->cors([
    'origin' => _env('FRONTEND_URL'),
    'methods' => ['GET', 'POST'],
]);

app()->post('/api/quest', function () {

    $visitedStages = app()->request()->get('stages') ?? null;
    $userAction = app()->request()->get('action') ?? null;
    $userStage = app()->request()->get('stage') ?? null;

    $service = new Neo4jService();

    if ($userAction) {
        $userAction = mb_ucfirst($userAction);
        $relation = Stage::selectOptionRelation($userAction, $visitedStages, $userStage);

        $stage = $service->getStageByAction($userAction, $relation);
        $options = $service->getStageOptions($stage[0]['quest.stage']);
    } else {
        $stage = $service->getStage($userStage);
        $options = $service->getStageOptions($userStage);
    }

    if ($visitedStages) {
       $options = Stage::filterSpecialConditionActions($options, $visitedStages, $userStage);
    }

    $result = [
        'stage' => $stage,
        'actions' => $options,
    ];

    response()->json($result);
});

app()->run();
