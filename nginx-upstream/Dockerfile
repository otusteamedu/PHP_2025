FROM nginx:latest

COPY ./hosts/nginx.conf /etc/nginx/nginx.conf
COPY ./hosts/balanser.conf /etc/nginx/conf.d/balanser.conf



WORKDIR /data

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]