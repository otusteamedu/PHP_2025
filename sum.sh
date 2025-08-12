#!/usr/bin/env bash

if [[ $# -ne 2 ]]; then
  echo "Error 2 arguments required"
  exit 1
fi


firstNumber=$1
secondNumber=$2

regex="^[+-]?[0-9]+([.][0-9]+)?\$"

if ! [[ "$firstNumber" =~ $regex ]] || ! [[ "$secondNumber" =~ $regex ]]; then
    echo "Input parameters must be numeric"
    exit 1
fi

sum=$(awk "BEGIN {print $firstNumber + $secondNumber}")
echo $sum