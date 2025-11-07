#!/bin/bash
if echo "$1" | grep -E -q "^[-+]?[0-9]*\.?[0-9]+$" && echo "$2" | grep -E -q "^[-+]?[0-9]*\.?[0-9]+$"; then
  c=$(awk "BEGIN {print $1 + $2}")
  echo "Сумма: $c"
else
  echo "Одна из переменных - это не число."; exit -1
fi