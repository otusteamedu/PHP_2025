FROM nginx:latest

#COPY ./hosts/mysite.local.conf /etc/nginx/conf.d/mysite.local.conf
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /data  

VOLUME /data  

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]