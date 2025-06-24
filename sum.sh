#!/bin/bash

if [ $# -ne 2 ]; then
  echo "Ошибка: необходимо ввести два числа." >&2
  exit 1
fi

num_regex='^-?[0-9]+([.][0-9]+)?$'

if ! [[ $1 =~ $num_regex ]] || ! [[ $2 =~ $num_regex ]]; then
  echo "Ошибка: оба аргумента должны быть числами." >&2
  exit 2
fi

sum=$(awk "BEGIN { print $1 + $2 }")
echo "$sum"