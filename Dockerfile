FROM php:8.2-fpm

RUN apt-get update && apt-get install -y git
# RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN pecl install pcov && docker-php-ext-enable pcov

RUN echo "pcov.enabled=1" >> /usr/local/etc/php/conf.d/pcov.ini && \
  echo "pcov.directory=/var/www" >> /usr/local/etc/php/conf.d/pcov.ini
