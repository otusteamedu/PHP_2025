FROM nginx:latest

COPY nginx.conf /etc/nginx/conf.d/nginx.conf

WORKDIR /var/www/dev

VOLUME /var/www/dev

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
