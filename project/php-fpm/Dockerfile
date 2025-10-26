FROM phpdockerio/php:8.4-fpm

WORKDIR "/app/home"
ENV TZ="Europe/Moscow"
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        git \
        curl \
        wget \
        procps \
        tzdata \
        php8.4-bcmath \
        php8.4-bz2 \
        php8.4-cgi \
        php8.4-gd \
        php8.4-imagick \
        php8.4-imap \
        php8.4-intl \
        php8.4-mcrypt \
        php8.4-memcached \
        php8.4-pgsql \
        php8.4-psr \
        php8.4-redis \
        php8.4-sqlite3 \
        php8.4-uuid \
        php8.4-xdebug \
        php8.4-yaml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    && cp /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone


