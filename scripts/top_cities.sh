#!/bin/bash

# Задаем путь к входному файлу с данными
FILE="data/user.txt"

# Проверяем наличие входного файла
if [ ! -f "$FILE" ]; then
  echo "Файл $FILE не найден!"
  exit 1
fi

# Выводим три самых популярных города
awk 'NR>1 { print $3 }' "$FILE" | sort | uniq -c | sort -nr | head -n 3