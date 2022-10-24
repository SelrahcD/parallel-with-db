<?php

$testToken = getenv('TEST_TOKEN') ?? 0;
$dbName = "testdb_$testToken";
putenv("DATABASE=$dbName");

$pdo = new \PDO("pgsql:host=localhost;port=5432;user=postgres;password=postgres");
$statement = $pdo->query(sprintf('SELECT count(*) FROM pg_database WHERE datname = \'%s\'', $dbName));
$countDB = $statement->fetchColumn();

if($countDB === 0) {
    $pdo->query(sprintf('CREATE DATABASE %s', $dbName));
}

