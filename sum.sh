#!/bin/bash

if [ "$#" -ne 2 ]; then
    echo 'Need 2 arguments!';
    exit 1;
fi

REGEX='^-?[0-9]+(\.[0-9]+)?$';

if ! [[ $1 =~ $REGEX ]] || ! [[ $2 =~ $REGEX ]]; then
    echo 'Arguments must be number!';
    exit 1;
fi

echo "Sum: $(awk "BEGIN {print $1 + $2}" )";
