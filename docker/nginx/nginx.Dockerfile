FROM nginx:stable-alpine

# Добавляем пользователя nginx в группу www-data
RUN addgroup nginx www-data

COPY ./nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./nginx/conf.d/ /etc/nginx/conf.d/
COPY ./src/ /var/www/html/

EXPOSE 80