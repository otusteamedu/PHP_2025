FROM redis:latest
LABEL authors="sgolovanov"

COPY redis.conf /usr/local/etc/redis/redis.conf
COPY users.acl /usr/local/etc/redis/users.acl

EXPOSE 6379

ARG REDIS_PASSWORD
ENV REDIS_PASSWORD=$REDIS_PASSWORD

CMD ["bash", "-c", "redis-server /usr/local/etc/redis/redis.conf --requirepass \"${REDIS_PASSWORD}\""]