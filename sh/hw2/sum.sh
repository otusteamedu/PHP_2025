#!/bin/bash

readonly ARGS_NUM=2

if [ "$#" -ne $ARGS_NUM ]; then
  echo "Количество аргументов скрипта должно быть равно ${ARGS_NUM}"; exit 1
fi

sum=0
invalid_args=false
args_regex="^[+-]?[0-9]+([.,][0-9]+)?$"

for ((i=1; i<="$#"; i++)); do
  if ! [[ ${!i} =~ $args_regex ]]; then
      invalid_args=true
      echo "Неподходящий аргумент скрипта (\$${i} = ${!i})"
      continue
  fi
  sum=$(awk -v sum="${sum}" -v arg_val="${!i/,/.}" 'BEGIN { sum += arg_val; sub(",", ".", sum); print sum }')
done

if $invalid_args; then
  echo "Аргументы скрипта должны быть действительными числами"; exit 1
else
  echo "Сумма аргументов скрипта равна ${sum}"
fi
