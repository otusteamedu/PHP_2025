#!/bin/bash

check_num () {
CHECK=`echo "$1" | sed 's/,/./' | grep -E ^\-?[0-9]*\.?[0-9]+$`

if [ "$CHECK" != '' ]; then
  echo $CHECK
else
 echo "error"
fi
}

if ! [ -x "$(command -v bc)" ]; then
  echo 'Надо установить пакет bc!' >&2
  exit 1
fi

FIRST_NUM=$(check_num $1)
if [ $FIRST_NUM == "error" ]; then
  echo "$1 - Это неподходящее число для суммирования!"  >&2
  exit 1
fi

SECOND_NUM=$(check_num $2)
if [ $SECOND-NUM == "error" ]; then
  echo "$2 - Это неподходящее число для суммирования!"  >&2
  exit 1
fi
echo "$FIRST_NUM+$SECOND_NUM" | bc

exit 0;
