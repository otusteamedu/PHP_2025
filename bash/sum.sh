#! /bin/bash
if [[ $1 =~ ^-?[[:digit:]]+\.?[[:digit:]]*$ ]] && [[ $2 =~ ^-?[[:digit:]]+\.?[[:digit:]]*$ ]]; then 
  # Разделяем числа на целую и дробную части
  int1=${1%.*};
  int2=${2%.*};
  if [[ $1 == *.* ]]; then
    frac1=${1#*.}
  else
    frac1=0
  fi

  if [[ $2 == *.* ]]; then
    frac2=${2#*.}
  else
    frac2=0
  fi

  digits_count_frac1=${#frac1}
  digits_count_frac2=${#frac2}

  # Если целая часть отрицательная, делаем дробную часть тоже отрицательной
  if [[ $int1 == -* ]]; then
    frac1="-$frac1"
  fi
  if [[ $int2 == -* ]]; then
    frac2="-$frac2"
  fi

  # Вычисляем максимальный порядок дробной части                
  if [[ $digits_count_frac1 == $digits_count_frac2 ]]; then
    multiplier=$digits_count_frac1
    number1=$(($int1 * 10**$multiplier + $frac1))
    number2=$(($int2 * 10**$multiplier + $frac2))
  else
    if [[ $digits_count_frac1 -gt $digits_count_frac2 ]]; then
      multiplier=$digits_count_frac1
      number1=$(($int1 * 10**$multiplier + $frac1))
      number2=$(($int2 * 10**$multiplier + $frac2 * 10**($multiplier - $digits_count_frac2)))
    else
      multiplier=$digits_count_frac2
      number1=$(($int1 * 10**$multiplier + $frac1 * 10**($multiplier - $digits_count_frac1)))
      number2=$(($int2 * 10**$multiplier + $frac2))
    fi
  fi

  printf %.3f\\n "$(($number1 + $number2))e-$multiplier"
else 
  echo "Invalid parameters!"
fi