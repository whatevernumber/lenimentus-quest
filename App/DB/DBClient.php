<?php

namespace App\DB;

interface DBClient
{
    public function initClient(): void;

    public function getClient(): mixed;

    public function runQuery(string $query, array $data): mixed;
}