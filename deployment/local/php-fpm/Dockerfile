FROM php:8.5-fpm

RUN apt-get update && apt-get install --assume-yes --no-install-recommends \
    curl \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath sockets gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename=composer

COPY . /var/www

WORKDIR /var/www
