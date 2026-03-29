#!/bin/bash

# Проверка наличия аргумента
if [ $# -ne 1 ]; then
  echo "Укажите файл: $0 <file>"
  exit 1
fi

file="$1"

# Проверка, что файл существует
if [ ! -f "$file" ]; then
  echo "Ошибка: '$file' не является файлом."
  exit 1
fi

# Обработка файла и вывод трех наиболее популярных городов
awk 'NR > 1 && NF {print $3}' "$file" | sort | uniq -c | sort -nr | head -n 3 | awk '{print $2}'
