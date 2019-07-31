FROM php:7.2-apache

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY web/ /var/www/html/
COPY example/ /var/www/html/

EXPOSE 80
