
FROM php:8.4-fpm-bullseye

RUN apt-get update && apt-get install -y librabbitmq-dev libssh-dev && pecl install amqp redis && docker-php-ext-enable amqp redis

#WORKDIR /app
#COPY . /app

#CMD ["php", "app.php"]
