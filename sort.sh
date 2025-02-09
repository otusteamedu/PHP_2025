#!/bin/bash

if [ -z  "$1" ]; then
    echo "Введите: $0 <название файла>"
    exit 1
fi

file=$1

if [ ! -f "$file" ]; then
    echo "Указанный файл не существует."
    exit 1
fi

if [ ! -r "$file" ]; then
    echo "Файл недоступен."
    exit 1
fi

if [ ! -s "$file" ]; then
    echo "Файл пустой."
    exit 1
fi

echo "3 наиболее популярных города среди пользователей системы $file:"
echo "----------------------------------"
echo ""

awk 'NR>1 && $3 ~ /[[:alnum:]]/ {print $3}' $file | sort | uniq -c | sort -nr | head -3

echo ""
