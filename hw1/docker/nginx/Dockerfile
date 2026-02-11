FROM nginx:alpine

ARG UID
ARG GID

RUN apk add --no-cache shadow && \
    usermod -u $UID nginx && \
    groupmod -g $GID nginx

# Удаляем дефолтный конфиг, который слушает порт 80
RUN rm -f /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/run/php && \
    touch /var/run/nginx.pid && \
    chown -R $UID:$GID /var/run/nginx.pid && \
    chown -R $UID:$GID /var/cache/nginx && \
    chown -R $UID:$GID /var/log/nginx && \
    chown -R $UID:$GID /etc/nginx/conf.d && \
    chown -R $UID:$GID /var/run/php

# Копируем конфигурацию (убедись, что в конфигах listen 8080)
COPY hosts/ /etc/nginx/conf.d/

# Проверка конфигурации перед запуском — хороший тон
RUN nginx -t

CMD ["nginx", "-g", "daemon off;"]
