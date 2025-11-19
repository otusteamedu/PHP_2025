FROM postgres:14

RUN localedef -i ru_RU -c -f UTF-8 -A /usr/share/locale/locale.alias ru_RU.UTF-8

ENV LANG=ru_RU.utf8

ENV POSTGRES_DB=tasks
ENV PGDATA=/var/lib/postgresql/data

EXPOSE 5432
