FROM nginx:latest

COPY nginx-balance.conf /etc/nginx/nginx.conf

WORKDIR /data

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]