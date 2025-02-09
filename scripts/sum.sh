#!/bin/bash

if [ "$#" -ne 2 ]; then
    echo "Использование: $0 <число1> <число2>"
    exit 1
fi

re='^-?[0-9]+([.][0-9]+)?$'

if [[ ! $1 =~ $re || ! $2 =~ $re ]]; then
    echo "Ошибка: оба аргумента должны быть числами."
    exit 1
fi

sum=$(echo "$1 + $2" | bc)
echo "Сумма: $sum"
