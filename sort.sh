#!/bin/bash

if [ ! -f "$1" ]
then
  echo "Файл не найден"
  exit 1
fi

if [ ! -r "$1" ]
then
  echo "Файл недоступен для чтения"
  exit 1
fi

awk 'NR>1 {print $3}' "$1" | sort | uniq -c | sort -nr | head -n 3