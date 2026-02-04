ARG POSTGRES_VERSION=12.4-alpine
FROM postgres:${POSTGRES_VERSION}

# Дополнительные настройки
ENV POSTGRES_INITDB_ARGS="--encoding=UTF8 --locale=C"
ENV PGDATA=/var/lib/postgresql/data/pgdata

# Копируем скрипты инициализации (опционально)
COPY init.sql /docker-entrypoint-initdb.d/
RUN chmod +r /docker-entrypoint-initdb.d/*

# Указываем healthcheck
HEALTHCHECK --interval=10s --timeout=5s --retries=5 \
  CMD pg_isready -U ${POSTGRES_USER} -d ${POSTGRES_DB}