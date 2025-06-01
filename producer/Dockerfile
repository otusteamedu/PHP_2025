
FROM php:8.2-cli

RUN apt-get update && apt-get install -y librabbitmq-dev libssh-dev     && pecl install amqp     && docker-php-ext-enable amqp

WORKDIR /app
COPY . /app

CMD ["php", "app.php"]
