FROM mysql:5.7

COPY ./docker/mysql/init.sql /docker-entrypoint-initdb.d/
