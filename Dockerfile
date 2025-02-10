FROM ubuntu:latest

WORKDIR /app

COPY . /app

# Устанавливаем необходимые утилиты (bc, awk, sort, uniq)
RUN apt update && apt install -y coreutils gawk bc

COPY top_cities.sh users.txt /app/

RUN chmod +x /app/sum.sh

ENTRYPOINT ["/bin/bash"]
