# QZ Laravel

Application that allows user to take a quiz or to submit one.

Try it:
  - https://qz-laravel.herokuapp.com/

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

--- 

***NOTE***
Sometimes right after starting the app it might return `502 Bad Gateway` error. 
This error appears because `Nginx` does not wait for the `app` to start.
`The issue can be resolved by refreshing the page.`

End-to-end testing setup uses `health checks` to solve this problem. 

---

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

--- 

***NOTE***
An existing `.env` file in `quiz` folder might cause config conflicts.
It is recommended to delete `.env` in `quiz` folder when setting up the project in `Docker`.

---