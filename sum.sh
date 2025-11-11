#!/bin/bash

if [ "$#" -ne 2 ]
then
  echo "Скрипт принимает на вход два числовых параметра: $0 <num1> <num2>"
  exit 1
fi

REGEX='^-?[0-9]+(\.[0-9]+)?$'

if [[ ! $1 =~ $REGEX || ! $2 =~ $REGEX ]]
then
  echo "Параметры должны быть числами"
  exit 1
fi

awk "BEGIN { print $1 + $2 }"