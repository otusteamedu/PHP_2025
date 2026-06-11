#!/usr/bin/bash

if [ "$#" -ne 1 ]
 then
  echo "Ошибка: необходимо указать путь к файлу с данными"
  exit 1
fi

FILE=$1

if [ ! -f "$FILE" ]; then
    echo "Ошибка: файл '$FILE' не найден."
    exit 1
fi

tail -n +2 "$FILE" | awk 'NF >= 3 {print $3}' | sort | uniq -c | sort -nr | head -n 3
