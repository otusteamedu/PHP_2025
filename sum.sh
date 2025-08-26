#!/usr/bin/env bash

if [ "$#" -ne 2 ]; then
  echo 'Usage: ./sum.sh <num1> <num2>';
  exit 1;
fi

SUM=0;

for number in "$@" ; do
  echo "$number";
  if [[ "$number" =~ ^-?[0-9]*$ ]]; then
      SUM=$(("$SUM" + "$number"));
  else
      echo "The input '$number' not number";
      exit 2;
  fi
done

echo "Sum is $SUM";
exit 0;