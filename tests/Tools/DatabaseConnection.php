<?php

declare(strict_types=1);

namespace Tests\SelrahcD\ParallelWithDb\Tools;

final class DatabaseConnection extends \PDO
{
    private int $transactionNesting = 0;

    private static self|null $instance = null;

    public static function getInstance(): self
    {
        if(!self::$instance) {
            self::$instance = new self("pgsql:host=localhost;port=5432;user=postgres;password=postgres");
        }

        return self::$instance;
    }

    public function beginTransaction(): bool
    {
        $this->transactionNesting++;

        if($this->transactionNesting === 1) {
            return parent::beginTransaction();
        }

        return true;
    }

    public function rollBack(): bool
    {
        $this->transactionNesting--;

        if($this->transactionNesting <= 0) {
            return parent::rollBack();
        }

        return true;
    }

    public function commit(): bool
    {
        $this->transactionNesting--;

        if($this->transactionNesting <= 0) {
            return parent::commit();
        }

        return true;
    }

}