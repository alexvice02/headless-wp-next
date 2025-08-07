FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
  git unzip zip curl libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev libpq-dev libfreetype6-dev \
  && docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite