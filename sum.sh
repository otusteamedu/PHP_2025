#!/usr/bin/env bash

FRAC=0
RESULT=0

for y in $@
do
  #длина строки аргумента
  STRLEN=$(echo -n $y | tr -d '\n' | wc -m)
  #длина строки аргумента, только числа и разделитель дробной части
  DIGIT=$(echo -n $y | grep -E "^[-]?[0-9]+[.]?[0-9]*$" | tr -d '\n' | wc -m)

  #Если размерность не совпадает, то это не число
  if [ $STRLEN -ne $DIGIT ]
  then
    echo -e "Аргумент \033[31m$y - не число!\033[0m"
    exit 0
  fi

  #Находим самую длинную строку после запятой
  A=$(echo -n $y | awk -F"." '{print $2}' | tr -d "\n" | wc -m)
  if [[ $A -ne 0 &&  $A -gt $FRAC ]]
  then
    FRAC=$A
  fi
done

if [ $FRAC -gt 0 ]
#если есть дробные числа
then
  for y in $@
  do
    NUMBER=0
    INT=$(echo -n $y | awk -F"." '{print $1}' | tr -d "\n")
    FLOAT=$(echo -n $y | awk -F"." '{print $2}' | tr -d "\n")
    FLOAT_LENGTH=$(echo -n $y | awk -F"." '{print $2}' | tr -d "\n" | wc -m)
    INT=$(($INT*$[10 ** $FRAC]))
    NUM_SIGN=$(echo -n $y | grep -E "^[-]{1}" | tr -d '\n' | wc -m)
    if [ $NUM_SIGN -ne 0 ]
      then NUM_SIGN=-1
      else NUM_SIGN=1
    fi

    if [ $FLOAT ]
        then
          #степень
          FACTOR=$(($FRAC-$FLOAT_LENGTH))
          FLOAT=$FLOAT*$((10**$FACTOR))*$NUM_SIGN

          NUMBER=$(($INT+$FLOAT))
        else
          NUMBER=$INT
        fi

    RESULT=$(($RESULT+$NUMBER))

  done
else
#если все числа целые
  for y in $@
    do
    RESULT=$(($RESULT+$y))
    done
fi

if [ $FRAC -gt 0 ]
then
  RESULT_LENGTH=$(echo -n $RESULT | wc -m)
  #размерность целой части: общая длина результата минус размерность дробной части
  INT_LENGTH=$(($RESULT_LENGTH-$FRAC))

  #целая часть числа
  INT="$(echo $RESULT | cut -c -$INT_LENGTH)"
  #дробная часть числа
  FLOAT="$(echo $RESULT | cut -c $(($INT_LENGTH+1))- )"

  RESULT="$INT.$FLOAT"

  echo $RESULT
else
  echo $RESULT
fi