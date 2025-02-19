#!/bin/bash

# Вывод ошибки и завершение (принимает текст ошибки)
function showError {
    printf "\033[91m[ERROR]\033[0m $1 \n"
    exit 1
}

# Проверка количества вводимых аргументов (допустимо только два аргумента)
if [ "$#" -ne 2 ]
then
    showError "Введите именно два числа"
fi

# Проверка аргументов регулярным выражением (допустимы только числа)
for var in "$@"
do
    if ! [[ "$var" =~ ^-?[0-9]+([.][0-9]+)?$ ]]
    then
        showError "\033[94m$var\033[0m не является числом"
    fi
done

# Вывод суммы двух чисел
echo "$1 $2" | awk '{print $1+$2}'
