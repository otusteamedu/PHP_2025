#!/bin/bash

# Проверка наличия необходимых утилит
for cmd in awk sort uniq head; do
    if ! command -v "$cmd" &> /dev/null; then
        echo "Ошибка: утилита '$cmd' не установлена. Пожалуйста, установите её."
        exit 1
    fi
done

# Проверка, что файл существует
FILE="city.txt"
if [ ! -f "$FILE" ]; then
    echo "Ошибка: файл '$FILE' не найден!"
    exit 1
fi

# Вывод 3 самых популярных городов
awk 'NR > 1 { print $3 }' city.txt | sort | uniq -c | sort -nr | awk 'NR > 1 {print $2}'| head -n 3