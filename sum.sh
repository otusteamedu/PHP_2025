#!/bin/bash

if [ $# -ne 2 ]; then
  echo "Error: Enter 2 numbers with a space";
  exit 1
fi

if ! [[ "$1" =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
  echo "Error: '$1' is not a 1 number"
  echo "Enter 2 numbers with a space!"
  exit 1
fi

if ! [[ "$2" =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
  echo "Error: '$2' is not a 2 number"
  echo "Enter 2 numbers with a space!"
  exit 1
fi

if dpkg -s bc &>/dev/null ; then
  sum=$(echo "$1 + $2" | bc)
else
  sum=$(awk "BEGIN {print $1 + $2}")
fi

echo "Sum of $1 and $2 = $sum"
