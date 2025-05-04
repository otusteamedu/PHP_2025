FROM postgres:15

RUN apt-get update && apt-get install -y \
    postgresql-contrib \
    && rm -rf /var/lib/apt/lists/*

# Копируем пользовательские конфигурационные файлы
COPY ./postgres/postgres.conf /etc/postgresql/postgresql.conf

# Установка переменных окружения, необходимых для работы Postgres
ENV POSTGRES_DB=my_database
ENV POSTGRES_USER=user
ENV POSTGRES_PASSWORD=example

# Указываем, где находятся конфигурационные файлы PostgreSQL
ENV POSTGRES_CONFIG=/etc/postgresql/postgresql.conf

CMD ["postgres", "-c", "config_file=/etc/postgresql/postgresql.conf"]
