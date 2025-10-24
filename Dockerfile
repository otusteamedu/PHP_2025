FROM php:8.2-fpm-alpine

ARG ENVIRONMENT=production
ARG COMPOSER_AUTH

RUN apk add --no-cache \
    nginx \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor

RUN docker-php-ext-install pdo pdo_mysql gd soap

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY src/ /var/www/html
COPY deploy/supervisor.conf /etc/supervisor/conf.d/supervisor.conf

RUN chown -R www-data:www-data /var/www/html

RUN if [ "$ENVIRONMENT" = "production" ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction; \
    else \
        composer install --optimize-autoloader --no-interaction; \
    fi


COPY deploy/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY deploy/setup-env.sh /usr/local/bin/setup-env.sh
RUN chmod +x /usr/local/bin/setup-env.sh

WORKDIR /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisor.conf"]