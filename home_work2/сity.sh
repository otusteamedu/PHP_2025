#!/bin/bash

if [ $# -ne 1 ]; then
    echo "Использование: $0 <файл_с_данными>" >&2
    exit 1
fi

FILE="$1"

if [ ! -f "$FILE" ]; then
    echo "Ошибка: файл '$FILE' не найден" >&2
    exit 1
fi

echo "Топ 3 города по количеству пользователей:"

awk 'NR>1 {print $3}' "$FILE" | sort | uniq -c | sort -rn | head -3 | while read count city; do
    echo "$city: $count пользователей"
done