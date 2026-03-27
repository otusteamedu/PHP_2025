#!/bin/bash

if [ $# -ne 2 ];
 then
  echo "Ошибка: необходимо ввести два числа." >&2
  exit 1
fi

REGULAR_EXPRESSION='^[+-]?[0-9]+(\.[0-9]+)?$'

if ! [[ $1 =~ $REGULAR_EXPRESSION ]] || ! [[ $2 =~ $REGULAR_EXPRESSION ]];
  then
    echo "Ошибка: оба аргумента должны быть числами." >&2
    exit 1
fi

SUM=$(awk "BEGIN { print $1 + $2 }")
echo "$SUM"
