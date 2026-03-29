#!/bin/bash

ANYERRROR=false;
if [[ ! $1 =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
    echo "Первый параметр не число"
    ANYERROR=true;
fi;
if [[ ! $2 =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
    echo "Второй параметр не число"
    ANYERROR=true;
fi;
if [ "$ANYERROR" = true ]; then
    exit 1;
fi
echo "$1        $2" | awk '{print $1 + $2}'