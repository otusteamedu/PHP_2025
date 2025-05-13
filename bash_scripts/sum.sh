#!/usr/bin/env bash

if [ "$#" -ne 2 ]; then
  echo "Ошибка: укажите два числа" >&2
  exit 1
fi

a="${1//,/.}"
b="${2//,/.}"

re='^[+-]?([0-9]+\.[0-9]*|[0-9]+|\.[0-9]+)$'

for x in "$a" "$b"; do
  [[ $x =~ $re ]] || { echo "Ошибка: '$x' не число" >&2; exit 1; }
done

sum=$(awk "BEGIN {print $a + $b}")

echo "$sum"
