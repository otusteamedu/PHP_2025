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

sum=$(echo "scale=2; $number1 + $number2" | bc -l)

echo "Сумма: $sum"