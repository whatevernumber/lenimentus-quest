<?php

namespace App\Services;

use App\db\DBClient;
use App\db\Neo4jClient;

class Neo4jService implements QuestDatabaseInterface
{
    private DBClient $client;

    public function __construct()
    {
        $this->client = new Neo4jClient();
    }

    /**
     * @param string $stage
     * @param string $text
     * @return mixed
     */
    public function createTextStage(string $stage, string $text): mixed
    {
        return $this->client->runQuery('CREATE (ts:TextStage {stage: $stage, text: $text})',
            [
                'stage' => $stage,
                'text' => $text,
            ]);
    }

    /**
     * @param string $stage
     * @param string $action
     * @return mixed
     */
    public function createOptionButtonWithLinkToTextStage(string $stage, string $action): mixed
    {
        return $this->client->runQuery('
                MATCH (ts:TextStage) WHERE ts.stage=$stage
                CREATE (ab:ActionButton {action: $action})
                CREATE (ts)-[:OPTION]->(ab)',
            [
                'stage' => $stage,
                'action' => $action,
            ]);
    }

    /**
     * @param string $stage
     * @param string $action
     * @return mixed
     */
    public function linkOptionButton(string $stage, string $action): mixed
    {
        return $this->client->runQuery('
                MATCH (ts:TextStage) WHERE ts.stage= $stage
                MATCH (ab:ActionButton) WHERE ab.action= $action
                CREATE (ts)-[:OPTION]->(ab)',
            [
                'stage' => $stage,
                'action' => $action,
            ]);
    }

    /**
     * @param string $stage
     * @param string $action
     * @return mixed
     */
    public function linkActionButton(string $stage, string $action): mixed
    {
        return $this->client->runQuery('
                MATCH (ts:TextStage) WHERE ts.stage= $stage
                MATCH (ab:ActionButton) WHERE ab.action= $action
                CREATE (ab)-[:ACTION]->(ts)',
            [
                'stage' => $stage,
                'action' => $action,
            ]);
    }

    /**
     * @param string $action
     * @param string $relation
     * @return array
     */
    public function getStageByAction(string $action, string $relation): array
    {
        return $this->client->runQuery("MATCH (quest:TextStage)<-[:$relation]-(a:ActionButton {action: \$action}) RETURN quest.stage, quest.text, quest.text_en",
            [
                'action' => $action,
            ]);
    }

    /**
     * @param string $stage
     * @return array
     */
    public function getStage(string $stage): array
    {
        return $this->client->runQuery('MATCH (quest:TextStage {stage: $stage}) RETURN quest.stage, quest.text, quest.text_en',
            [
                'stage' => $stage,
            ]);
    }

    /**
     * @param string $stage
     * @return array
     */
    public function getStageOptions(string $stage): array
    {
        return $this->client->runQuery('MATCH (ee:TextStage {stage: $stage})-[:OPTION]->(quest) 
            OPTIONAL MATCH (quest)-[:ACTION]->(ss:TextStage)
            OPTIONAL MATCH (quest)-[:ACTION_A]->(a:TextStage)
            OPTIONAL MATCH (quest)-[:ACTION_B]->(b:TextStage)
            RETURN quest.action, quest.action_en, ss.stage, a.stage, b.stage',
            [
                'stage' => $stage,
            ]);
    }

    /**
     * @param string $stage
     * @param string $text
     * @param string $languageField
     * @return array
     */
    public function updateStageText(string $stage, string $text, string $languageField): array
    {
        return $this->client->runQuery('MATCH (ee:TextStage {stage: $stage})
            SET ee += $patch
            RETURN ee',
            [
                'stage' => $stage,
                'patch' => [$languageField => $text],
            ]);
    }

    /**
     * @param string $stage
     * @param string $action
     * @param string $newAction
     * @param string $languageField
     * @return array
     */
    public function updateAction(string $stage, string $action, string $newAction, string $languageField): array
    {
        return $this->client->runQuery('MATCH (ee:TextStage)-[:OPTION]->(b:ActionButton) 
            WHERE ee.stage= $stage and b.action = $action 
            SET b += $patch
            RETURN ee',
            [
                'stage' => $stage,
                'action' => $action,
                'patch' => [$languageField => $newAction],
            ]);
    }

    /**
     * @return array
     */
    public function getTextStages(): array
    {
        return $this->client->runQuery("MATCH (ee:TextStage) RETURN ee", []);
    }
}