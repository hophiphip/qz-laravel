# Quiz app

## Requirements
  - [PHP](https://www.php.net/)
  - [Composer](https://getcomposer.org/download/)
  - [Laravel](https://laravel.com/)
  - [MongoDB](https://www.mongodb.com/)


## ~~Slow~~ Quick start
Instructions for running the app locally.

### Install Laravel MongoDB Package
```sh
sudo pecl install mongodb
```

Ensure that the mongodb extension is enabled in `php.ini` file.
Add the following line to `php.init` file.
```sh
extension="mongodb.so"
```

You can find `php.ini` file location by running
```sh
php --ini
```

### Install app dependencies
```sh
composer install
```

### Create application `.env` file and generate the app key
Create app `.env` file
```sh
cp .env.example.local .env
```

---

**NOTE**
`.env` files for `Docker` and local setup are different. In case of `Docker` setup errors the solution might be to delete an existing `.env` file.

---

Generate the app key
```sh
php artisan key:generate
```

### Change `.env` file if necessary and run migrations
```sh
php artisan migrate
```

### Start the app
```
php artisan serve --host=0.0.0.0 --port=8080
```
