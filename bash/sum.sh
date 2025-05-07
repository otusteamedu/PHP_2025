#!/usr/bin/bash

if [ "$#" -ne 2 ]
 then
  echo "Ошибка: необходимо ввести два аргумента."
  exit 1
fi

REGULAR_EXP='^-?[0-9]+([.][0-9]+)?$'

for arg in "$@"; do
    if ! [[ "$arg" =~ $REGULAR_EXP ]]; then
        echo "Ошибка: '$arg' не является числом."
        exit 1
    fi
done

SUM=$(awk "BEGIN {print $1 + $2}")

echo "Сумма чисел: $SUM"
