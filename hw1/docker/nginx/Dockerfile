FROM nginx:latest

RUN rm /etc/nginx/conf.d/default.conf

COPY site.conf /etc/nginx/conf.d/default.conf

WORKDIR /data

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]