FROM nginx:alpine

RUN rm /etc/nginx/conf.d/default.conf
COPY template-conf.d /etc/nginx/templates

WORKDIR /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]