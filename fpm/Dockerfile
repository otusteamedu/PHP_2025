FROM php:8.4-fpm

RUN apt-get update && apt-get install nano -y
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer