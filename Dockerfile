FROM php:7.1-fpm

RUN apt-get update && apt-get install -y --force-yes \
    git \
    libbz2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libicu-dev \
    libpq-dev \
    curl \
    vim \
    zip

WORKDIR /var/www/html

COPY . ./

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer global require "hirak/prestissimo:^0.3"

RUN composer install --no-interaction \
	&& composer clear-cache

RUN mkdir -p var/cache var/log var/sessions \
	&& chown -R www-data var
