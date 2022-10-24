# Run tests in parallel with a database

This is a simple demo project to show how to run tests in parallel with tests using a database.

A database is a shared resource used by multiple tests. When we run tests in parallel, some actions made at the database level may conflict, leading to flaky tests - tests that sometimes pass and sometimes don't. Having flaky tests in a test suite decrease the value of the test suite as it lowers our confidence (Is this test failing for a problem or just because it's colliding with another test ?) and slow us down as you have to repeatedly run the test suites to see if the outcome differs each time.

## Run tests

First, start the database container with `docker-compose up -d', then run `./vendor/bin/paratest` to run the tests in parallel.
