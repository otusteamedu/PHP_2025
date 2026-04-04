#!/bin/bash

if [ $# != 2 ]; then
  echo "Неверное число аргументов"
  exit 1
fi

for arg in "$@"
do
  if ! [[ "$arg" =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
    echo "$arg не является числом"
    exit 1
  fi
done

sum=$(awk "BEGIN {print $1 + $2}")

echo "$sum"

