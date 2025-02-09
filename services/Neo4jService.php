<?php

namespace Services;

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\ClientBuilder;

class Neo4jService
{
    private string $user;
    private string $password;
    private string $db;

    private ClientInterface $client;

    public function __construct()
    {
        $this->user = _env('DB_USER');
        $this->password = _env('DB_PASSWORD');
        $this->db = _env('DB_DATABASE');
        $this->createClient();
    }

    /**
     * @return void
     */
    public function createClient(): void
    {
        $credits = $this->user . ':' . $this->password;

        $this->client = ClientBuilder::create()->withDriver('bolt', 'bolt://' . $credits . '@89.191.225.149:7687') // creates a bolt driver
        ->withDriver('http', 'http://89.191.225.149', Authenticate::basic('neo4j', $this->password)) // creates an http driver
        ->withDriver('neo4j', 'neo4j://neo4j.test.com?database=' . $this->db, Authenticate::oidc('token')) // creates an auto routed driver with an OpenID Connect token
        ->withDefaultDriver('bolt')
        ->build();
    }

    /**
     * @param string $stage
     * @param string $text
     * @return void
     */
    public function createTextStage(string $stage, string $text): void
    {
        $this->client->run("CREATE (ts:TextStage {stage:'" . $stage . "', text: '" . $text . "'})");
    }

    /**
     * @param string $stage
     * @param string $action
     * @return void
     */
    public function createAndLinkOptionButton(string $stage, string $action): void
    {
        $this->client->run("
                    MATCH (ts:TextStage) WHERE ts.stage='" . $stage . "'
                    CREATE (ab:ActionButton {action: '" . $action . "'})
                    CREATE (ts)-[:OPTION]->(ab)");
    }

    /**
     * @param string $stage
     * @param string $action
     * @return void
     */
    public function linkOptionButton(string $stage, string $action): void
    {
        $this->client->run("
                MATCH (ts:TextStage) WHERE ts.stage='" . $stage . "'
                MATCH (ab:ActionButton) WHERE ab.action='" . $action . "'
                CREATE (ts)-[:OPTION]->(ab)");
    }

    /**
     * @param string $stage
     * @param string $action
     * @return void
     */
    public function linkActionButton(string $stage, string $action): void
    {
        $this->client->run("
                MATCH (ts:TextStage) WHERE ts.stage='" . $stage . "'
                MATCH (ab:ActionButton) WHERE ab.action='" . $action ."'
                CREATE (ab)-[:ACTION]->(ts)
        ");
    }

    /**
     * @param string $action
     * @param string $relation
     * @return array
     */
    public function getStageByAction(string $action, string $relation): array
    {
        return $this->client->run("MATCH (quest:TextStage)<-[:" . $relation . "]-(a:ActionButton {action:'" . $action . "'}) RETURN quest.stage, quest.text")->toArray();
    }

    /**
     * @param string $stage
     * @return array
     */
    public function getStage(string $stage): array
    {
        return $this->client->run("MATCH (quest:TextStage {stage: '" . $stage . "'}) RETURN quest.stage, quest.text")->toArray();
    }

    /**
     * @param string $stage
     * @return array
     */
    public function getStageOptions(string $stage): array
    {
        return $this->client->run("MATCH (ee:TextStage {stage: '" . $stage ."'})-[:OPTION]->(quest) 
        OPTIONAL MATCH (quest)-[:ACTION]->(ss:TextStage)
        OPTIONAL MATCH (quest)-[:ACTION_A]->(a:TextStage)
        OPTIONAL MATCH (quest)-[:ACTION_B]->(b:TextStage)
        RETURN quest.action, ss.stage, a.stage, b.stage")
        ->toArray();
    }
}