FROM nginx:stable-alpine

# Копируем начальные конфигурации
COPY /nginx.conf /etc/nginx/nginx.conf
COPY /default.conf /etc/nginx/conf.d/default.conf

WORKDIR /data

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]