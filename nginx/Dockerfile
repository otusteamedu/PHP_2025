FROM nginx:latest

COPY ./hosts/otus-php.local.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

VOLUME /app

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]