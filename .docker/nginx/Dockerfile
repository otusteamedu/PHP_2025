FROM nginx

COPY ./fkh.local.conf /etc/nginx/conf.d/fkh.local.conf

WORKDIR /data

VOLUME /data

EXPOSE 80

ENTRYPOINT ["nginx", "-g", "daemon off;"]
