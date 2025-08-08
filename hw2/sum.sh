#!/bin/bash

# Проверка: должно быть ровно 2 аргумента
if [ "$#" -ne 2 ]; then
  echo "❌ Error: Provide exactly two arguments." >&2
  exit 1
fi

# Регулярка для числа: поддерживает отрицательные и дробные
reg='^-?[0-9]+([.][0-9]+)?$'

# Проверка первого аргумента
if ! [[ $1 =~ $reg ]]; then
  echo "❌ Error: '$1' is not a valid number." >&2
  exit 1
fi

# Проверка второго аргумента
if ! [[ $2 =~ $reg ]]; then
  echo "❌ Error: '$2' is not a valid number." >&2
  exit 1
fi

# Складываем (bc работает с дробями)
sum=$(echo "$1 + $2" | bc -l)
echo "$sum"

# Использование ниже
#chmod +x sum.sh
#./sum.sh 1.5 -7