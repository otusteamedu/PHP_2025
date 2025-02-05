FROM nginx:latest

COPY conf.d/nginx.conf /etc/nginx/conf.d/nginx.conf

WORKDIR /data

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
