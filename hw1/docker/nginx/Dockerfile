FROM nginx:alpine

# Устанавливаем правильные права для пользователя nginx
RUN apk add --no-cache shadow && \
    usermod -u 1000 nginx && \
    groupmod -g 1000 nginx

# Копируем конфигурацию
COPY hosts/ /etc/nginx/conf.d/

EXPOSE 80 443