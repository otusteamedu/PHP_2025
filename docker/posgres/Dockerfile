FROM postgres:14.3

RUN localedef -i ru_RU -c -f UTF-8 -A /usr/share/locale/locale.alias ru_RU.UTF-8

ENV LANG=ru_RU.utf8
ENV TZ=Europe/Minsk

EXPOSE 5432
