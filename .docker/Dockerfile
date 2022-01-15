FROM laradock/php-fpm:latest-8.0

ARG user
ARG uid

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
      apt-utils \
      gnupg2 \
      git \
      libzip-dev zip unzip && \
      docker-php-ext-configure zip && \
      docker-php-ext-install zip && \
      php -m | grep -q 'zip' && \
      chmod +x /usr/local/bin/install-php-extensions && sync && \
      install-php-extensions mongodb-stable


# NOTE: This might cause problems later
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN groupadd -g $uid $user
RUN useradd -u $uid -ms /bin/bash -g $user $user

COPY ./quiz /var/www
COPY --chown=$user:$user quiz /var/www
WORKDIR /var/www

USER $user

CMD ["sh", "-c", "cp -n .env.example.docker .env && composer install && php artisan key:generate && php artisan migrate:refresh --seed && php-fpm"]

EXPOSE 9000
