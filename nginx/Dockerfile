FROM nginx:latest

# Если нам нужно чтобы скрипт работал на 80 порту, то надо удалить дефолтный конфиг nginx
# RUN rm /etc/nginx/conf.d/default.conf

COPY ./hosts/mysite.local.conf /etc/nginx/conf.d/mysite.local.conf

WORKDIR /data  

VOLUME /data  

EXPOSE 8080

CMD ["nginx", "-g", "daemon off;"]