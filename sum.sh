#!/bin/bash

if [ -z "$1" ] || [ -z "$2" ]; then
  echo "Error: Enter 2 numbers with a space."
  exit 1
fi

if ! echo "$1" | grep -qE '^-?[0-9]+(\.[0-9]+)?$'; then
  echo "Error: Parameter \$1 is not a valid number."
  exit 1
fi

if ! echo "$2" | grep -qE '^-?[0-9]+(\.[0-9]+)?$'; then
  echo "Error: Parameter \$2 is not a valid number."
  exit 1
fi

result=$(awk "BEGIN {print $1 + $2}")

echo "Sum of numbers: $result"
