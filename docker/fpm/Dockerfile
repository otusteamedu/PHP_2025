FROM php:8.2-fpm

RUN apt-get update \
	&& apt-get install -y dos2unix libmemcached-dev zlib1g-dev \
	&& pecl install redis memcached \
	&& docker-php-ext-enable redis memcached \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./php-fpm.conf /usr/local/etc/php-fpm.conf

# Позволяем запускать composer от root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Копируем entrypoint и даём права
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /data
VOLUME /data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]