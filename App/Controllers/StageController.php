<?php

namespace App\Controllers;

use App\Classes\Stage;
use Leaf\Controller;
use App\Services\Neo4jService;

class StageController extends Controller
{
    /**
     * @return void
     * @throws \Exception
     */
    public function processAction(): void
    {
        $visitedStages = app()->request()->get('stages') ?? null;
        $currentAction = app()->request()->get('action') ?? null;
        $currentStage = app()->request()->get('stage') ?? null;

        $service = new Neo4jService();

        if ($currentAction) {
            $currentAction = mb_ucfirst($currentAction);
            $relation = Stage::selectOptionRelation($currentAction, $visitedStages, $currentStage);

            if (!in_array($relation, ['ACTION', 'ACTION_A', 'ACTION_B'])) {
                throw new \Exception('unknown relation');
            }

            $stage = $service->getStageByAction($currentAction, $relation);
            $options = $service->getStageOptions($stage[0]['quest.stage']);
        } else {
            $stage = $service->getStage($currentStage);
            $options = $service->getStageOptions($currentStage);
        }

        if ($visitedStages) {
            $options = Stage::filterSpecialConditionActions($options, $visitedStages, $currentStage);
        }

        response()->json([
            'stage' => $stage,
            'actions' => $options,
        ]);
    }
}