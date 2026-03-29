#!/bin/bash

file="$1"

if [ ! -f "$file" ]; then
  echo "Ошибка: '$file' не файл."
  exit 1
fi

awk 'NR > 1 {print $3}' "$file" | sort | uniq -c | sort -nr | head -n 3 | awk '{print $2}'