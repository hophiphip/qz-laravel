# QZ Laravel

Application that allows user to take a quiz or to submit one.

## Requirements
  - [Docker](https://www.docker.com/)

## Quick start
Setup `.env` file.
```sh
cp ./quiz/.env.example.docker .env
```

Start the application.
```sh
docker-compose up -d
```

## Unit and feature tests
Run tests after starting the app.
```sh
docker-compose exec app php artisan test
```

## End-to-End tests
1. [Setup `.env` file if necessary](#quick-start).
2. Run end-to-end tests.
```shell
docker-compose -f docker-compose-e2e.yml up --exit-code-from cypress
```