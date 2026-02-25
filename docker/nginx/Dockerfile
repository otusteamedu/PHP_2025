FROM nginx:latest

COPY mysite.local.conf /etc/nginx/conf.d/mysite.local.conf

WORKDIR /var/www/mysite/public

VOLUME /var/www/mysite/public

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]