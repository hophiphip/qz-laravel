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

## Cypress tests
...
