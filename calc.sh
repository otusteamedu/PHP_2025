#!/bin/bash

if ! command -v awk &> /dev/null; then
    echo "ОШИБКА: awk не установлен на вашей системе"
    echo "Установите пакет"
    echo "После установки запустите скрипт снова"
    exit 1
fi

get_number() {
    local message=$1
    local number=$2
    
    while true; do
        read -p "$message" input
        
        if [ -z "$message" ]; then
            echo "Вы ничего не ввели. Попробуйте еще раз."
            continue
        elif [[ $input =~ ^-?[0-9]*\.?[0-9]+$ ]]; then
            eval "$number=$input"
            break
        else
            echo "Ошибка! '$input' не является числом. Попробуйте еще раз."
        fi
    done
}

get_number "Введите первое число: " first

while true; do
    read -p "Введите действие (сложить(s/+) или вычесть (d/-)): " action
    
    if [ -z "$action" ]; then
        echo "Вы ничего не ввели. Попробуйте еще раз."
        continue
    elif [[ "$action" == "s" || "$action" == "+" ]] then
        frendly_action="Сумма: "
        real_action="+"
        break
    elif [[ "$action" == "d" || "$action" == "-" ]]; then
        frendly_action="Разность: "
        real_action="-"
        break
    else
        echo "Ошибка! Действие $action недоступно. Ведите одно из разрешенных действий"
    fi
done
get_number "Введите второе число: " second

res=$(awk "BEGIN {print $first $real_action $second}")

echo $frendly_action $res
