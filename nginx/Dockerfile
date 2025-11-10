FROM nginx

WORKDIR /app

VOLUME /app

COPY ./hosts/mysite.local.conf /etc/nginx/conf.d/mysite.local.conf

EXPOSE 80
EXPOSE 6379

CMD ["nginx", "-g", "daemon off;"]