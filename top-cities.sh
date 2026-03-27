#!/bin/bash

if [ ! -f "$1" ]; then
  echo "Ошибка: укажите имя существующего файла с таблицей." >&2
  exit 1
fi

awk 'NR>1 {print $3}' "$1" | sort | uniq -c| sort -nr | head -n 3
