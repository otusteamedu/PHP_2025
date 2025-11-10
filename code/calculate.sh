#!/bin/sh
printf "Введите значение первого числа: "
read -r a

if ! echo "$a" | grep -qE '^-?[0-9]+(\.[0-9]+)?$'; then
  echo "Ошибка: 'a' не является числом."
  exit 1
fi

printf "Введите значение второго числа: "
read -r b

if ! echo "$b" | grep -qE '^-?[0-9]+(\.[0-9]+)?$'; then
  echo "Ошибка: 'b' не является числом."
  exit 1
fi

result=$(awk "BEGIN {print $a + $b}" )
echo "Сумма чисел $a и $b равна: $result"