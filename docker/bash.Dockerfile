FROM alpine:latest

# Устанавливаем bash, bc и coreutils (для sort, head и т.п.)
RUN apk add --no-cache bash coreutils bc

# Создаем симлмнки для удобного вызова
RUN ln -s /app/scripts/sum.sh /usr/local/bin/sum \
    && ln -s /app/scripts/top_cities.sh /usr/local/bin/top-cities

# Рабочая директория
WORKDIR /app

CMD ["bash"]