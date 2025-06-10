FROM nginx:latest

COPY mysite.local.conf /etc/nginx/conf.d/mysite.local.conf

WORKDIR /var/www/public

VOLUME /var/www/public

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]