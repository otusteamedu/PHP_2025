FROM nginx:stable-alpine

# Копируем начальные конфигурации
COPY /conf/nginx.conf /etc/nginx/nginx.conf
COPY /conf/default.conf /etc/nginx/conf.d/default.conf
COPY /conf/upstreams.conf /etc/nginx/conf.d/upstreams.conf

WORKDIR /var/www/public

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]