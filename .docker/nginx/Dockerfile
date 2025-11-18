FROM nginx:alpine

COPY ./.docker/nginx/conf.d /etc/nginx/conf.d
COPY ./application /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]