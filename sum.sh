#!/bin/bash

if [ $# -ne 2 ]; then
  echo "Нужно указать два числа"
  exit 1
fi

is_number() {
  echo "$1" | grep -Eq "^-?[0-9]+(\.[0-9]+)?$"
}

if ! is_number "$1" || ! is_number "$2"; then
  echo "Аргументы должны быть числами"
  exit 1
fi

sum=$(awk "BEGIN {print $1 + $2}")
echo "$sum"
