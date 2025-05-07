#!/usr/bin/env bash

var1=$1
var2=$2

if ! [[ "$var1" =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
    echo "Error: first argument is not a number"
    exit 1
fi

if ! [[ "$var2" =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
    echo "Error: second argument is not a number"
    exit 1
fi

sum=$(awk "BEGIN {print $var1 + $var2}")

echo $sum