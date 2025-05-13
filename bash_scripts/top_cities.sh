#!/usr/bin/env bash

if [ "$#" -ne 1 ]; then
  echo "Ошибка: укажите файл с таблицей" >&2
  echo "Использование: $0 <файл>" >&2
  exit 1
fi

file="$1"

if [ ! -f "$file" ]; then
  echo "Ошибка: файл '$file' не найден" >&2
  exit 1
fi

awk 'NR>1 { print $3 }' "$file" |
  sort |
  uniq -c |
  sort -rn |
  head -n 3 |
  awk '{ print $2 }'
