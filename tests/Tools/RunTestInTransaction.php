<?php

declare(strict_types=1);

namespace Tests\SelrahcD\ParallelWithDb\Tools;

use PHPUnit\Runner\AfterTestHook;
use PHPUnit\Runner\BeforeTestHook;

final class RunTestInTransaction implements BeforeTestHook, AfterTestHook
{
    public function executeBeforeTest(string $test): void
    {
        DatabaseConnection::getInstance()->beginTransaction();
    }

    public function executeAfterTest(string $test, float $time): void
    {
        DatabaseConnection::getInstance()->rollBack();
    }
}