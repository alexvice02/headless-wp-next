FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip zip curl libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev libpq-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo mysqli pdo_mysql pdo_pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY ./backend /var/www/html

CMD ["composer", "install"]
CMD ["php-fpm"]
