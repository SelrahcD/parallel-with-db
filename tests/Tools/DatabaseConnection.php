<?php

declare(strict_types=1);

namespace Tests\SelrahcD\ParallelWithDb\Tools;

final class DatabaseConnection extends \PDO
{

    public function __construct()
    {
        parent::__construct(sprintf("pgsql:host=localhost;port=5432;user=postgres;password=postgres;dbname=%s", getenv('DATABASE')));
    }

}