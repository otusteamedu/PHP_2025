#!/bin/bash

number1=$1
number2=$2

is_number() {
  [[ $1 =~ ^-?[0-9]+([.][0-9]+)?$ ]]
}

if ! is_number "$number1"; then
    echo "Ошибка: '$number1' не является числом"
    exit 1
fi

if ! is_number "$number2"; then
    echo "Ошибка: '$number2' не является числом"
    exit 1
fi

if ! command -v bc >/dev/null 2>&1; then
  echo "Ошибка: bc не установлен. Необходимо использовать целые числа<br>" >&2

  if [[ "$number1" =~ ^-?[0-9]+$ ]]; then
    echo "$number1 — не целое число<br>"
    exit 1
  elif [[ "$number1" =~ ^-?[0-9]+$ ]]; then
    echo "$number2 — не целое число<br>"
    exit 1
  fi
fi

sum=$(echo "scale=2; $number1 + $number2" | bc -l)

echo "Сумма: $sum"