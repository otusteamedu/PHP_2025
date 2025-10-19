FROM php:8.2-cli

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y git unzip \
    && docker-php-ext-install sockets

#CMD ["php", "worker/ReportWorker.php"]