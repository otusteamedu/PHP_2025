FROM redis:latest

COPY redis.conf /usr/local/etc/redis/redis.conf

WORKDIR /data

VOLUME /data

EXPOSE ${REDIS_PORT}

CMD ["/bin/sh", "-c", "sed -i \"s/^port 6379 .*/port $REDIS_PORT/\" /usr/local/etc/redis/redis.conf && redis-server /usr/local/etc/redis/redis.conf"]
