#!/bin/bash

if [ $# -ne 2 ]; then
    echo "Ошибка: необходимо 2 числа" >&2
    exit 1
fi

for arg in "$1" "$2"; do
    if ! [[ $arg =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
        echo "Ошибка: '$arg' - не число" >&2
        exit 1
    fi
done

if command -v awk &> /dev/null; then
    sum=$(awk "BEGIN {print $1 + $2}")
    echo "$sum"
else
    if [[ $1 =~ ^-?[0-9]+$ ]] && [[ $2 =~ ^-?[0-9]+$ ]]; then
        echo $(($1 + $2))
    else
        echo "Ошибка: установите awk для работы с вещественными числами" >&2
        exit 1
    fi
fi