#!/bin/bash

if [ "$#" -ne 2 ]; then
    echo "Input only 2 digits"
    exit 1
fi

for var in $@
do
  if ! [[ $var =~ ^[+-]?[0-9]+[.]?[0-9]+$ ]]; then
    echo "Enter ($var): it's not a number"
    exit 1
  fi
done

sum=$(echo $1 $2 | awk '{print $1 + $2}')

echo "Result: $sum"
exit 0