# Run tests in parallel with a database

This is a simple demo project to show how to run tests in parallel with tests using a database.

> **Warning**
> This is demo code. Its purpose is to demonstrate the ideas and is not what I would call production worthy code.

## The problem

A database is a shared resource used for multiple tests. Good practices of cleaning the database state after each test prevent from having interdependent tests when tests are run sequentially. Unfortunately, when tests are run in parallel, these good practices are not enough because some actions made for one test in one process might conflict at the database level with actions made for another test in another process. This leads to flaky tests - tests that sometimes pass and sometimes don't. Having flaky tests in a test suite decreases the value of the test suite as it lowers our confidence (Is this test failing for a problem or just because it's colliding with another test ?) and slows us down as you have to repeatedly run the test suites to see if the outcome differs each time.

### Experiment

If you want to follow along, checkout the [`conflicting-tests` branch](https://github.com/SelrahcD/parallel-with-db/tree/conflicting-tests).

This demo project contains a simple failing case: we want to store ponies inside a `ponies` table, but their names have to be unique. To make things simpler, there is no business logic, and all the code is in [the test](./tests/PonyRepositoryTest.php). The test inserts a new pony in the table and then counts that we have one pony that goes by that name. Also, after each test, the `ponies` table is emptied, which is a mandatory step for tests to be able to run sequentially.

A test with the same pony name is repeated in [another file](./tests/PonyRepository2Test.php) to allow us to see the issue when they are run in parallel.

The database connection is (sort-of/really slightly) abstracted in the [`DatabaseConnection`](./tests/Tools/DatabaseConnection.php) class for convenience.

The [database schema](./tests/Tools/schema.sql) is created before the first test using [a PhpUnit extension](./tests/Tools/RunMigrationBeforeFirstTest.php).

If you want to run the examples, you'll first need to install the dependencies using `composer install` and start the database with `docker-compose up -d'.

#### No problem in sequence

Run the tests sequentially using PhpUnit, with `./vendor/bin/phpunit`. You'll see that the tests are running without any issues.

#### Problem in parallel.

Now, run the tests in parallel using Paratest, `./vendor/bin/paratest`. A test should[^1] be failing with the error

```
PDOException: SQLSTATE[23505]: Unique violation: 7 ERROR:  duplicate key value violates unique constraint "ponies_name_key"
DETAIL:  Key (name)=(Spirit) already exists.
```


If you don't see an error try again a few times.

[^1]: On my machine, it failed every time, which is a little bit strange. I hoped that the clean-up in one of the `tearDown` methods would pass before starting the other test from time to time.

If you run the test multiple times, you'll notice that the failing test is either in the `PonyRepositoryTest` or in `PonyRepository2Test` test class. This is a real case of flaky tests interacting with each other via a shared resource.

### Stop sharing the resource
>If a shared resource bothers you, just stop sharing it.

That's nice advice, but how can we do this with a database?
Do we need to use a different database for every test?

Of course not! We've seen that the problem occurs only for tests running in parallel and that tests running sequentially are working perfectly fine, which means we only need a different database for each test running process.

Now, move to the [`main` branch](https://github.com/SelrahcD/parallel-with-db/) again and run the tests in parallel with `/vendor/bin/paratest`.

Tests do not fail anymore!

What have we changed?

We've modified the [`DatabaseConnection`](./tests/Tools/DatabaseConnection.php) class, our little abstraction on top of the `PDO` database connection. Now, it doesn't connect to the default database anymore. Instead, it gets the name of the database from the `DATABASE` environment variable and uses that name.

We've also added a [bootstrap file](./bootstrap.php) which is executed before tests are run. When we use PhpUnit, that file is executed once. When Paratest runs the tests in parallel, that file is executed once per process (plus one time before that). Also, Paratest adds a `TEST_TOKEN` environment variable which gives us the id of the current test process. In the bootstrap file we:
- get the value of the `TEST_TOKEN` environment variable
- coalesce it to 0 in case we're not in a Paratest test process
- create a database name as a concatenation of a prefix and the test token
- store the database name in the `DATABASE` environment variable we're reading when creating a database connection
- query the database server to know if a database exists with our new database name
- if there is no database with that name, we create one.

And now, each process gets its own database. Thanks to the logic in the bootstrap file, if the number of processes is different from machine to machine, the correct amount of databases is created[^2].

[^2]: Actually, one extra database is created because the first bootstrap is not in a Paratest test process. Not dealing with that case is interesting as it allows us to keep the same logic when tests are run with PhpUnit.
