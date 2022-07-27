FROM php:8.1-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip build-essential \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu redis \
    && docker-php-ext-enable apcu redis \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

SHELL ["/bin/bash", "--login", "-c"]

RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
RUN nvm install 16.16.0
RUN npm install --global yarn


WORKDIR /var/www/project

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

COPY php.ini-development /usr/local/etc/php/php.ini
