#!/bin/sh

if [ "$#" -ne 2 ]; then
    echo "Принимается только два аргумента."
    exit 1
fi
result=0
regexp='^[+-]?[0-9]+([.][0-9]+)?$'
for i in $@
    do
      if ! [[ $i =~ $regexp ]]; then
        echo "Аргумент '$i' не является числом."
        exit 1
      fi
    result=$(awk "BEGIN { print $i + $result }")
    done

echo $result
exit 0
