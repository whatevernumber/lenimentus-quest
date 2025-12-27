<?php

namespace Classes;

class Stage
{
    const array SPECIAL_CHOICES_LEVELS = [
        '3.1.1' => self::WINDOW_AVAILABLE_REQUIRED_LEVEL,
        '3.1.2' => self::WINDOW_AVAILABLE_REQUIRED_LEVEL,
        '3.2.1' => self::ROOF_AVAILABLE_REQUIRED_LEVEL,
        '3.2.2' => self::ROOF_AVAILABLE_REQUIRED_LEVEL,
        '3.3' => self::ROOF_AVAILABLE_REQUIRED_LEVEL,
        self::BEFORE_JENNY_STAGE => self::JENNY_NEWS_STAGE
    ];

    const string WINDOW_AVAILABLE_REQUIRED_LEVEL = '1.2';
    const string ROOF_AVAILABLE_REQUIRED_LEVEL = '1.3';
    const string JENNY_NEWS_STAGE = '2.1.1';
    const string BEFORE_JENNY_STAGE = '6';

    const array LADDER_CHOICES_LEVEL = ['3.2.1', '3.2.2', '3.3'];
    const array WINDOW_CHOICES_LEVELS = ['3.1.1', '3.1.2'];

    const string STAGE_ROOF_ACTION = 'Подняться на крышу';
    const string STAGE_WAIT_ACTION = 'Ждать';

    const string STAGE_ASK_ACTION = '«Извините, вы мне кого-то напоминаете…»';
    const string ACTION_RELATION_STRING = 'ACTION';

    /**
     * @param string $action
     * @param array $visitedStages
     * @param string $currentStage
     * @return string
     */
    static function selectOptionRelation(string $action, array $visitedStages, string $currentStage): string
    {
        if ($action !== Stage::STAGE_WAIT_ACTION && in_array($currentStage, Stage::WINDOW_CHOICES_LEVELS)) {
            return self::ACTION_RELATION_STRING . (self::isSpecialRoute($visitedStages, $currentStage) ? '_A' : '_B');
        }

        return self::ACTION_RELATION_STRING;
    }

    /**
     * @param array $visitedStages
     * @param string $stage
     * @return bool
     */
    static function isSpecialRoute(array $visitedStages, string $stage): bool
    {
        return in_array(self::SPECIAL_CHOICES_LEVELS[$stage], $visitedStages);
    }

    /**
     * @param array $options
     * @param array $visitedStages
     * @param string $currentStage
     * @return array
     */
    static function filterSpecialConditionActions(array $options, array $visitedStages, string $currentStage): array
    {
        if (in_array($currentStage, Stage::LADDER_CHOICES_LEVEL) && !empty($options) && !self::isSpecialRoute($visitedStages, $currentStage)) {
            $options = array_filter($options, function ($option) {
                return $option['quest.action'] !== Stage::STAGE_ROOF_ACTION;
            });

            $options = array_values($options);
        }

        if ($currentStage === Stage::BEFORE_JENNY_STAGE && !empty($options) && !self::isSpecialRoute($visitedStages, $currentStage)) {
            $options = array_filter($options, function ($option) {
                return $option['quest.action'] !== Stage::STAGE_ASK_ACTION;
            });

            $options = array_values($options);
        }

        return $options;
    }
}