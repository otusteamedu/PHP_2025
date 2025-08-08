#!/usr/bin/env bash

set -euo pipefail

if [[ $# -ne 2 ]]; then
  echo "Usage: ./sum.sh <number1> <number2>" >&2
  exit 1
fi

number_regex='^[+-]?[0-9]+([.][0-9]+)?$'

if [[ ! $1 =~ $number_regex ]] || [[ ! $2 =~ $number_regex ]]; then
  echo "Error: both arguments must be numbers (integers or decimals like 1.5, -7)" >&2
  exit 1
fi

awk -v a="$1" -v b="$2" 'BEGIN { print a + b }'
