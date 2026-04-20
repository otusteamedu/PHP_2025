#!/bin/env bash
num1=$1
num2=$2

is_valid_number() {
    [[ $1 =~ ^-?(0|[1-9][0-9]*)(\.[0-9]+)?$ ]]
}

if ! is_valid_number "$num1"; then
    echo "Ошибка: '$num1' — некорректное число."
    exit 0
fi

if ! is_valid_number "$num2"; then
    echo "Ошибка: '$num2' — некорректное число."
    exit 0
fi

sum=$(awk "BEGIN { print $num1 + $num2 }")
echo "$sum"
