FROM nginx:1.19-alpine

# Удаляем все стандартные конфиги
RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/sites-enabled/*

# Копируем наш конфиг
COPY hosts/mysite.local.conf /etc/nginx/conf.d/