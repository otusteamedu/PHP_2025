FROM nginx:alpine

# Устанавливаем правильные права для пользователя nginx
RUN apk add --no-cache shadow && \
    usermod -u 1000 nginx && \
    groupmod -g 1000 nginx

# Копируем конфигурацию
COPY hosts/ /etc/nginx/conf.d/

# Создаем директорию для логов
RUN mkdir -p /var/log/nginx && \
    touch /var/log/nginx/access.log /var/log/nginx/error.log && \
    chown -R nginx:nginx /var/log/nginx

EXPOSE 80 443