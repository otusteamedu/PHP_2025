#!/bin/bash

is_number() {
    local re='^[+-]?[0-9]+([.][0-9]+)?$'
    [[ $1 =~ $re ]] && return 0 || return 1
}

ONE_NUMBER=$1
TWO_NUMBER=$2

if ! is_number "$ONE_NUMBER";  then
echo “Первый аргумент длолжен быть числом!”
exit;
fi

if ! is_number "$TWO_NUMBER";  then
echo “Второй аргумент длолжен быть числом”
exit;
fi

echo $(awk "BEGIN {print $ONE_NUMBER + $TWO_NUMBER}");

