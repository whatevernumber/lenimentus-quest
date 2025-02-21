<?php

namespace Classes;

class Stage
{
    const SPECIAL_CHOICES_LEVELS = [
        '3.1.1' => self::WINDOW_AVAILABLE_REQUIRED_LEVEL,
        '3.1.2' => self::WINDOW_AVAILABLE_REQUIRED_LEVEL,
        '3.2.1' => self::ROOF_AVAILABLE_REQUIRED_LEVEL,
        '3.2.2' => self::ROOF_AVAILABLE_REQUIRED_LEVEL,
        '3.3' => self::ROOF_AVAILABLE_REQUIRED_LEVEL,
        self::BEFORE_JENNY_STAGE => self::JENNY_NEWS_STAGE
    ];

    const WINDOW_AVAILABLE_REQUIRED_LEVEL = '1.2';
    const ROOF_AVAILABLE_REQUIRED_LEVEL = '1.3';
    const JENNY_NEWS_STAGE = '2.1.1';
    const BEFORE_JENNY_STAGE = '6';

    const LADDER_CHOICES_LEVEL = ['3.2.1', '3.2.2', '3.3'];
    const WINDOW_CHOICES_LEVELS = ['3.1.1', '3.1.2'];

    const STAGE_ROOF_ACTION = 'Подняться на крышу';
    const STAGE_WAIT_ACTION = 'Ждать';

    const STAGE_ASK_ACTION = '«Извините, вы мне кого-то напоминаете…»';
    const ACTION_RELATION_STRING = 'ACTION';

    /**
     * @param string $action
     * @param array $visitedStages
     * @param string $userStage
     * @return string
     */
    static function selectOptionRelation(string $action, array $visitedStages, string $userStage): string
    {
        if ($action !== Stage::STAGE_WAIT_ACTION && in_array($userStage, Stage::WINDOW_CHOICES_LEVELS)) {
            return self::ACTION_RELATION_STRING . (self::selectRoute($visitedStages, $userStage) ? '_A' : '_B');
        }

        return self::ACTION_RELATION_STRING;
    }

    /**
     * @param array $visitedStages
     * @param string $stage
     * @return string
     */
    static function selectRoute(array $visitedStages, string $stage): string
    {
        return in_array(self::SPECIAL_CHOICES_LEVELS[$stage], $visitedStages);
    }

    /**
     * @param array $options
     * @param array $visitedStages
     * @param string $userStage
     * @return array
     */
    static function filterSpecialConditionActions(array $options, array $visitedStages, string $userStage): array
    {
        if (in_array($userStage, Stage::LADDER_CHOICES_LEVEL) && !empty($options) && !self::selectRoute($visitedStages, $userStage)) {
            $options = array_filter($options, function ($option) {
                return $option['quest.action'] !== Stage::STAGE_ROOF_ACTION;
            });

            $options = array_values($options);
        }

        if ($userStage === Stage::BEFORE_JENNY_STAGE && !empty($options) && !self::selectRoute($visitedStages, $userStage)) {
            $options = array_filter($options, function ($option) {
                return $option['quest.action'] !== Stage::STAGE_ASK_ACTION;
            });

            $options = array_values($options);
        }

        return $options;
    }
}