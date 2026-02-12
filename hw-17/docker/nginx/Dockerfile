FROM ubuntu:latest

RUN apt-get update  
RUN apt-get install -y nginx

COPY hosts/nginx.conf /etc/nginx/sites-enabled/nginx.conf

WORKDIR /data

VOLUME /data

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
