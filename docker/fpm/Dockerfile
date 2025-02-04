FROM php:8.4-fpm

# ставим необходимые для нормальной работы модули
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
		libpng-dev \
		libonig-dev \
		libzip-dev \
		libmemcached-dev \
		libmcrypt-dev \
		zip \
    	unzip \
    	curl \
		libz-dev \
		zlib1g-dev \
		libssl-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
		&& pecl install redis memcached \
		&& docker-php-ext-enable redis memcached \
		&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
