<?php

declare(strict_types=1);

namespace Tests\SelrahcD\ParallelWithDb\Tools;

use PHPUnit\Runner\BeforeFirstTestHook;

final class RunMigrationBeforeFirstTest implements BeforeFirstTestHook
{

    public function executeBeforeFirstTest(): void
    {
        echo "Create database schema" . PHP_EOL;

        (DatabaseConnection::getInstance())->exec(file_get_contents(__DIR__ . '/schema.sql'));

        echo "Database schema created" . PHP_EOL;
    }
}