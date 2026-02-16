#!/usr/bin/env bash

if [ "$#" -ne 2 ]; then
  echo 'Usage: ./sum.sh <num1> <num2>';
  exit 1;
fi

SUM=0;
USE_BC=0;
if command -v bc >/dev/null 2>&1; then
  echo "Calculating using bc";
  USE_BC=1;
elif command -v awk >/dev/null 2>&1; then
  echo "Calculating using awk";
else
  echo "awk or bc required to run this script";
  exit 3;
fi

for number in "$@" ; do
  if [[ "$number" =~ ^[+-]?[0-9]+([.][0-9]+)?$ ]]; then
    if [[ $USE_BC -eq 1 ]]; then
      SUM=$(echo "$SUM + $number" | bc -l);
    else
      SUM=$(awk "BEGIN { print $SUM + $number }");
    fi
  else
      echo "The input '$number' not number";
      exit 2;
  fi
done

echo "$SUM" | xargs printf "SUM is %.2f";
exit 0;