FROM redis:latest

RUN apt-get update && apt-get install -y gettext-base && apt-get clean

COPY redis.conf /usr/local/etc/redis/redis.conf.template
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]