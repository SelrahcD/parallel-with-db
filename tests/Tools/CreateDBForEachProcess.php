<?php

declare(strict_types=1);

namespace Tests\SelrahcD\ParallelWithDb\Tools;

use PHPUnit\Runner\AfterLastTestHook as AfterLastTestHookAlias;
use PHPUnit\Runner\BeforeFirstTestHook;

final class CreateDBForEachProcess implements BeforeFirstTestHook, AfterLastTestHookAlias
{

    public function executeBeforeFirstTest(): void
    {
        $pdo = new \PDO("pgsql:host=localhost;port=5432;user=postgres;password=postgres");
        $dbName = str_replace('.', '_', uniqid("testdb_", true));
        putenv("DATABASE={$dbName}");

        $statement = $pdo->query(sprintf('SELECT count(*) FROM pg_database WHERE datname = \'%s\'', $dbName));
        $countDB = $statement->fetchColumn();

        if($countDB === 0) {
            $pdo->query(sprintf('CREATE DATABASE %s', $dbName));
        }
    }

    public function executeAfterLastTest(): void
    {
        $pdo = new \PDO("pgsql:host=localhost;port=5432;user=postgres;password=postgres");
        $pdo->query(sprintf('DROP DATABASE %s', getenv('DATABASE')));
    }
}