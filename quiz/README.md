# Quiz app

## Requirements
  - [PHP](https://www.php.net/)
  - [Composer](https://getcomposer.org/download/)
  - [Laravel](https://laravel.com/)
  - [MongoDB](https://www.mongodb.com/)


## ~~Slow~~ Quick start
Instructions for running the app locally.

### Install Laravel MongoDB Package
```shell
sudo pecl install mongodb
```

Ensure that the mongodb extension is enabled in `php.ini` file.
Add the following line to `php.init` file.
```shell
extension="mongodb.so"
```

You can find `php.ini` file location by running
```shell
php --ini
```

### Install app dependencies
```shell
composer install
```

### Create application `.env` file and generate the app key
Create app `.env` file
```shell
cp .env.example.local .env
```

---

**NOTE**
`.env` files for `Docker` and local setup are different. In case of `Docker` setup errors the solution might be to delete an existing `.env` file.

---

Generate the app key
```shell
php artisan key:generate
```

### Change `.env` file if necessary and run migrations
```shell
php artisan migrate
```

### Start the app
```shell
php artisan serve --host=0.0.0.0 --port=8080
```

---

**NOTE**
Setup local MongoDB with `Docker`.
```shell
docker run -d --name quizdb -p 27017:27017 -e MONGO_INITDB_ROOT_USERNAME=${MONGO_DB_USERNAME} -e MONGO_INITDB_ROOT_PASSWORD=${MONGO_DB_PASSWORD} -e MONGO_INITDB_DATABASE=${MONGO_DB_AUTHENTICATION_DATABASE} mongo
```

---

### Run Cypress tests
Start the app and then run 
```shell
CYPRESS_BASE_URL=http://127.0.0.1:8000 ./node_modules/cypress/bin/cypress open
```
---

***NOTE***
You may change `CYPRESS_BASE_URL` if necessary. But by default when running the app with `php artisan serve` it  will run on `127.0.0.1:8000`.

---
