#!/bin/bash 

if [ $# -ne 2 ]
then 
    echo "Введите два числа"
    exit -1
fi

if ! [[ $1 =~ ^-?[0-9]+([.][0-9]+)?$ ]] || ! [[ $2 =~ ^-?[0-9]+([.][0-9]+)?$ ]]; then
    echo "Ошибка: оба аргумента должны быть числами"
    exit -1
fi

result=$(awk "BEGIN { print $1 + $2 }")

if [[ $result == .* ]]; then
    result="0$result"
fi

echo "$result"