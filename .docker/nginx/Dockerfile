FROM nginx:alpine

COPY ./conf.d /etc/nginx/conf.d

WORKDIR /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]