#!/bin/bash

if [ $# -ne 2 ]
then
  echo "Ожидается два аргумента"
  exit 1
fi

OPERAND_1=$1
OPERAND_2=$2

function isNumber() {
  re='^-?[0-9]+([.][0-9]+)?$'
  [[ $1 =~ $re ]]
}

if ! isNumber "$OPERAND_1";
then
  echo "Первый операнд не является числом"
  exit 1
fi

if ! isNumber "$OPERAND_2";
then
  echo "Второй операнд не является числом"
  exit 1
fi

echo -e "$1\n $2" | awk '{sum+=$1} END {print sum}'