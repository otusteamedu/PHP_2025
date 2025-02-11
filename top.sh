#!/bin/bash

file="$1"

if [ ! -f "$file" ]; then
  echo "Аргумент не является файлом"
  exit 1
fi

awk 'NR>1 && NF {print $3}' "$file" | sort | uniq -c | sort -nr | head -3 | awk '{print $2}'
