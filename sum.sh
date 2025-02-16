!#/bin/bash

IFS='-' read -r -a parts <<< "$1"

error=0
for part in "${parts[@]}"; do
  if [[ $part =~ ^-?[0-9]+(\.[0-9]+)?$ ]]; then
      sum=$(awk "BEGIN {print $sum + $part}")
    else
      error=$((error + 1))
      echo "Строка '$part' НЕ является числом."
    fi
done

if [[ error -gt 0 ]]; then
    echo "Ошибка! $error значений не является числом."
    echo "Все, что удалось подсчитать: $sum"
  else
    echo "Общая сумма: $sum"
  fi
