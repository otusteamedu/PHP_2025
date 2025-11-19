FROM php:8.0

WORKDIR /app

COPY . .

CMD ["php", "-S", "0.0.0.0:8000"]