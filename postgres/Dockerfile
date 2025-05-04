FROM postgres:15

RUN apt-get update && apt-get install -y \
    postgresql-contrib \
    && rm -rf /var/lib/apt/lists/*


# Установка переменных окружения
ENV POSTGRES_DB=my_database
ENV POSTGRES_USER=user
ENV POSTGRES_PASSWORD=example

CMD ["postgres"]