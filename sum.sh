#!/usr/bin/bash

reg='^-?[0-9]+(\.[0-9]+)?$'
if [[ $1 =~ $reg && $2 =~ $reg ]]; then
  echo $1
  echo $2
  SUM=$(awk -v a="$1" -v b="$2" 'BEGIN {print a+b}')
  echo "$SUM"
else
  echo "Invalid arithmetic expression"
fi