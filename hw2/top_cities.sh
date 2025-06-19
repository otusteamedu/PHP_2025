#!/bin/bash

# Проверка: передан ли файл
if [ $# -ne 1 ]; then
  echo "❌ Usage: $0 <filename>" >&2
  exit 1
fi

file="$1"

# Проверка: существует ли файл
if [ ! -f "$file" ]; then
  echo "❌ File '$file' not found." >&2
  exit 1
fi

# Извлекаем колонку с городами, пропускаем заголовок, считаем и сортируем
awk 'NR>1 { print $3 }' "$file" | \
  sort | \
  uniq -c | \
  sort -nr | \
  head -n 3 | \
  awk '{ print $2, $1}'

# Использование скрипта
#chmod +x top_cities.sh
#./top_cities.sh users.txt