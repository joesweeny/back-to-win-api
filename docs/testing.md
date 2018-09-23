# Testing
This application uses PHPUnit and is well tested with a good coverage of Integration and Unit tests.

To execute the full test suite the executable command is:

`$ bin/docker-dev-test`

Executing this command runs the test suite in the dedicated `test` docker container. The reason for
the use of a dedicated container is to isolate the test suite away from the local development application.
Additional features and benefits of this separation are:

- SQLite (in memory) is used for the persistence layer tests to improve the speed of the test suite
- Any classes that require integration with external services i.e. Redis use a `log` driver to stub
out responses
- Storage testing uses a local implementation instead of using third parties which are used in the staging
and production environments