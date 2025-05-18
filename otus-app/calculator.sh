#!/bin/bash

sum=0
regex="^[-+]{0,1}[0-9]+([.]{0,1}[0-9]+)?$"

if [ $# -ne 2 ]
then
  echo "invalid argument count"
  exit -1
fi

for i in $@
do
  if ! [[ $i =~ $regex ]]; then
      echo "invalid argument $i"
      exit -1
  fi
  sum=$(awk "BEGIN { print $sum + $i }")
done

echo "Result: $sum"
