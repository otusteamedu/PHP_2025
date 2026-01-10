#!/bin/bash

if [ -z "$1" ]; then
  echo "Укажите путь к файлу"
  exit 1
fi

FILE="$1"

if [ ! -f "$FILE" ]; then
  echo "Файл не существует или это не обычный файл"
  exit 1
fi

if file "$FILE" | grep -qv "text"; then
  echo "Это не текстовый файл"
  exit 1
fi

awk 'NR > 1 && NF {print $3}' "$FILE" | sort | uniq -c | sort -nr | head -n 3
