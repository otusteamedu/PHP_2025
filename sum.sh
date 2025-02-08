#!/bin/bash

if [ "$#" -ne 2 ]; then
    echo 'Need 2 arguments!';
    exit 1;
fi

if ! [[ $1 =~ ^-?[0-9]+(\.[0-9]+)?$ ]] || ! [[ $2 =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
    echo 'Arguments must be number!';
    exit 1;
fi

sum=$(awk "BEGIN {print $1 + $2}" );
echo "Sum: $sum";
