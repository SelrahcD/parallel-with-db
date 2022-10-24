<?php

declare(strict_types=1);

namespace Tests\SelrahcD\ParallelWithDb;

use PHPUnit\Framework\TestCase;
use Tests\SelrahcD\ParallelWithDb\Tools\DatabaseConnection;

class PonyRepositoryTest extends TestCase
{
    protected function tearDown(): void
    {
        $connection = new DatabaseConnection();

        echo "Clean ponies table" . PHP_EOL;
        $connection->query("DELETE FROM ponies");
        echo "Ponies table cleaned" . PHP_EOL;
    }

    /**
    * @test
    */
    public function stores_a_pony_with_same_name_as_in_other_test_file(): void {

        $connection = new DatabaseConnection();

        $connection->query("INSERT INTO ponies (name) VALUES ('Spirit')");

        $statement = $connection->query("SELECT count(*) FROM ponies WHERE name = 'Spirit'");

        $this->assertEquals(1, $statement->fetchColumn());
    }

    /**
     * @test
     */
    public function stores_a_pony_with_same_name_as_in_this_test_file_1(): void {

        $connection = new DatabaseConnection();

        $connection->query("INSERT INTO ponies (name) VALUES ('Griotte')");

        $statement = $connection->query("SELECT count(*) FROM ponies WHERE name = 'Griotte'");

        $this->assertEquals(1, $statement->fetchColumn());
    }

    /**
     * @test
     */
    public function stores_a_pony_with_same_name_as_in_this_test_file_2(): void {

        $connection = new DatabaseConnection();

        $connection->query("INSERT INTO ponies (name) VALUES ('Griotte')");

        $statement = $connection->query("SELECT count(*) FROM ponies WHERE name = 'Griotte'");

        $this->assertEquals(1, $statement->fetchColumn());
    }
}
