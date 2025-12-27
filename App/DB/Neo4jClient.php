<?php

namespace App\DB;

use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\ClientBuilder;
use Laudis\Neo4j\Contracts\ClientInterface;

class Neo4jClient implements DBClient
{
    private string $user;
    private string $password;
    private string $db;
    private string $dbUrl;
    private ClientInterface $client;

    public function __construct()
    {
        $this->user = _env('DB_USER');
        $this->password = _env('DB_PASSWORD');
        $this->db = _env('DB_DATABASE');
        $this->dbUrl = _env('DB_URL');
        $this->initClient();
    }

    /**
     * @return void
     */
    public function initClient(): void
    {
        $credits = $this->user . ':' . $this->password;

        $this->client = ClientBuilder::create()->withDriver('bolt', 'bolt://' . $credits . '@' . $this->dbUrl) // creates a bolt driver
            ->withDriver('http', 'http://' . $this->dbUrl, Authenticate::basic('neo4j', $this->password)) // creates an http driver
            ->withDriver('neo4j', 'neo4j://neo4j.test.com?database=' . $this->db, Authenticate::oidc('token')) // creates an auto routed driver with an OpenID Connect token
            ->withDefaultDriver('bolt')
            ->build();
    }

    /**
     * @param string $query
     * @param array $data
     * @return array
     */
    public function runQuery(string $query, array $data): array
    {
        return $this->client->run($query, $data)->toArray();
    }
}