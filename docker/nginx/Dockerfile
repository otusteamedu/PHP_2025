FROM nginx:1.28-alpine
LABEL authors="Artem Plekhanov"

# Копируем начальные конфигурации
COPY /conf/nginx.conf /etc/nginx/nginx.conf
COPY /conf/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]