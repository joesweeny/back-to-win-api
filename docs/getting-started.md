# Getting Started
In order to get started on this application you will need the following installed locally  for local
development:
- Docker
- Docker-Compose
- Composer

Once installed the next step is to install the project dependencies by executing:

`$ composer install --ignore-platform-reqs`

To build a running application for local development there are handy executable scripts available in
the `bin` directory. The first command to execute will be:

`$ bin/docker-dev-up -d --build`

This command uses the `docker-compose.yml` file to build the required containerized  services for local
development with `-d` forcing the application to run in daemon mode (in the background). Once this command
is executed you will be presented with the URL, in the terminal, to access the application in the browser.
It will look like:

```
Starting backtowinapi_redis_1 ...
Starting backtowinapi_mysql_1 ... done
Starting backtowinapi_web_1 ...
Starting backtowinapi_migrate_1 ...
Starting backtowinapi_web_1 ... done
Starting backtowinapi_nginx-proxy_1 ... done
Running daemon mode:
    proxy:    https://0.0.0.0:32774
```
