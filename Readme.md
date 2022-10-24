# Run tests in parallel with a database

This is a simple demo project to show how to run tests in parallel with tests using a database.

## The problem

A database is a shared resource used by multiple tests. When we run tests in parallel, some actions made at the database level may conflict, leading to flaky tests - tests that sometimes pass and sometimes don't. Having flaky tests in a test suite decrease the value of the test suite as it lowers our confidence (Is this test failing for a problem or just because it's colliding with another test ?) and slow us down as you have to repeatedly run the test suites to see if the outcome differs each time.

### Experiment

This demo project contains a simple failing case: we want to store ponies inside a `ponies` table, but their name have to be unique. To make things simpler there is no business logic and all the code is in [the test](./tests/PonyRepositoryTest.php). The test insert a new pony in the table, and the count that we have one pony that goes by that name. Also, after each test the `ponies` table is emptied, which is a mandatory step for tests to be able to run sequentially. 

A test with the same pony name is repeated in [another file](./tests/PonyRepository2Test.php) to allow us to see the issue when they are run in parallel.

The database connection is (sort-of/really slightly abstracted) in the [`DatabaseConnection`](./tests/Tools/DatabaseConnection.php) class for convenience.

The [database schema](./tests/Tools/schema.sql) is created before first test using [a PhpUnit extension](./tests/Tools/RunMigrationBeforeFirstTest.php).


If you want to run the examples you'll first need to install the dependencies using `composer install` and start the database with `docker-compose up -d`.

#### No problem in sequence

First checkout the `conflicting-tests` branch and run the tests sequentially using PhpUnit, with `./vendor/bin/phpunit`. You'll see that the tests are running without any issue.

#### Problem in parallel.

Now, run the tests in parallel using Paratest, `./vendor/bin/paratest`. A test should[^1] be failing with the error

```
PDOException: SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint "ponies_name_key"
DETAIL:  Key (name)=(Spirit) already exists.
```

[^1]: On my machine it failed every time, which is a little bit strange. I hoped that from time to time the clean-up in the 

If you run the test multiple times you'll notice that the failing test is either in the `PonyRepositoryTest` or in `PonyRepository2Test` test class. This is a real case of flaky tests interacting one with each other via a shared resource.

### Stop sharing the resource
>If a shared resource bothers you just stop sharing it.

That's a nice advice but how can we do this with a database ?
Do we need to use a different database for every test ?

Of course not! We've seen that the problem occurs only for tests running in parallel, and that tests running sequentially are working perfectly fine, which means we only need a different database for each test running process.

Move to the ``

## Run tests

First, start the database container with `docker-compose up -d', then run `./vendor/bin/paratest` to run the tests in parallel.

