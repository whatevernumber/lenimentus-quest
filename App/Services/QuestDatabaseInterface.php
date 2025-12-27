<?php

namespace App\Services;

interface QuestDatabaseInterface
{
    /**
     * @param string $stage
     * @param string $text
     * @return mixed
     */
    public function createTextStage(string $stage, string $text): mixed;

    /**
     * @param string $stage
     * @param string $action
     * @return mixed
     */
    public function createOptionButtonWithLinkToTextStage(string $stage, string $action): mixed;

    /**
     * @param string $stage
     * @param string $action
     * @return mixed
     */
    public function linkOptionButton(string $stage, string $action): mixed;

    /**
     * @param string $stage
     * @param string $action
     * @return mixed
     */
    public function linkActionButton(string $stage, string $action): mixed;

    /**
     * @param string $action
     * @param string $relation
     * @return mixed
     */
    public function getStageByAction(string $action, string $relation): mixed;

    /**
     * @param string $stage
     * @return array
     */
    public function getStage(string $stage): array;

    /**
     * @param string $stage
     * @return array
     */
    public function getStageOptions(string $stage): array;

    /**
     * @param string $stage
     * @param string $text
     * @param string $languageField
     * @return array
     */
    public function updateStageText(string $stage, string $text, string $languageField): array;

    /**
     * @param string $stage
     * @param string $action
     * @param string $newAction
     * @param string $languageField
     * @return array
     */
    public function updateAction(string $stage, string $action, string $newAction, string $languageField): array;

    /**
     * @return array
     */
    public function getTextStages(): array;
}