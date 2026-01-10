FROM nginx:latest

COPY ./conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

CMD ["nginx", "-g", "daemon off;"]