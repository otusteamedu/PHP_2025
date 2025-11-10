#!/bin/bash

# Подключаем файл с таблицей жителей
FILE="users.txt"

if [ ! -f "$FILE" ]; then
    echo "Ошибка: Файл $FILE не найден!"
    exit 1
fi

echo "3 лучших города:"

awk 'NR > 1 {print $3}' "$FILE" |
sort |
uniq -c |
sort -nr |
head -3 |
while read count city; do
    printf "%-15s: %d пользователей\n" "$city" "$count"
done