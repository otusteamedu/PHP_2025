FROM nginx:latest
LABEL authors="SGolovanov"

COPY ./hosts/mysite.local.conf /etc/nginx/conf.d/default.conf

WORKDIR /data

VOLUME /data

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
