#!/bin/bash
AWK_PATH=`which awk`
if [ -z $AWK_PATH ]; then
    echo "Утилита awk не установлена!"
    exit -1
fi
OPERAND1="$1"
OPERAND2="$2"
REG_EXP="^[+-]?([0-9]+\.?[0-9]*|\.[0-9]+)$"
if ! [[ $OPERAND1 =~ $REG_EXP ]]; then
    echo "Операнд-1 не является числом!"
    exit -2
fi
if ! [[ $OPERAND2 =~ $REG_EXP ]]; then
    echo "Операнд-2 не является числом!"
    exit -2
fi
RESULT=`echo "$OPERAND1 $OPERAND2" | awk '{print $1 + $2}'`
echo "Результат сложения: $RESULT"
