<?php

declare(strict_types=1);

namespace Tests\SelrahcD\ParallelWithDb;

use PHPUnit\Framework\TestCase;
use Tests\SelrahcD\ParallelWithDb\Tools\DatabaseConnection;

class PonyRepository2Test extends TestCase
{
    protected function tearDown(): void
    {
        $connection = DatabaseConnection::getInstance();

        echo "Clean ponies table" . PHP_EOL;
        $connection->query("DELETE FROM ponies");
        echo "Ponies table cleaned" . PHP_EOL;
    }

    /**
    * @test
    */
    public function stores_a_pony_with_same_name_as_in_other_test_file(): void {

        $connection = DatabaseConnection::getInstance();

        $connection->query("INSERT INTO ponies (name) VALUES ('Spirit')");

        $statement = $connection->query("SELECT count(*) FROM ponies WHERE name = 'Spirit'");
        
        $this->assertEquals(1, $statement->fetchColumn());
    }
}
