<?php


require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

app()->cors([
    'origin' => _env('FRONTEND_URL'),
    'methods' => ['GET', 'POST'],
]);

app()->post('/api/quest', 'App\Controllers\StageController@processAction');

    $visitedStages = app()->request()->get('stages') ?? null;
    $currentAction = app()->request()->get('action') ?? null;
    $currentStage = app()->request()->get('stage') ?? null;

    $service = new Neo4jService();

    if ($currentAction) {
        $currentAction = mb_ucfirst($currentAction);
        $relation = Stage::selectOptionRelation($currentAction, $visitedStages, $currentStage);

        $stage = $service->getStageByAction($currentAction, $relation);
        $options = $service->getStageOptions($stage[0]['quest.stage']);
    } else {
        $stage = $service->getStage($currentStage);
        $options = $service->getStageOptions($currentStage);
    }

    if ($visitedStages) {
       $options = Stage::filterSpecialConditionActions($options, $visitedStages, $currentStage);
    }

    $result = [
        'stage' => $stage,
        'actions' => $options,
    ];

    response()->json($result);
});

app()->run();
