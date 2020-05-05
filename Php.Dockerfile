FROM php:7.2-fpm

RUN apt-get update -y
RUN apt-get install libsodium-dev -y
RUN docker-php-ext-install pdo pdo_mysql sodium