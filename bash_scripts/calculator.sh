#!/bin/bash
echo -n 'Enter the First Number: '
read -r a
echo -n 'Enter the Second Number: '
read -r b

#checking the format of numbers
REGEX="^[+-]?[0-9]+([.][0-9]+)?$"
if [[ ! $a =~ $REGEX ]];
  then echo 'Error: First Number has wrong format' >&2; exit 1
elif [[ ! $b =~ $REGEX ]];
  then echo 'Error: Second Number has wrong format' >&2; exit 1
fi

echo "$a" "$b" | awk '{print $1+$2}'

exit 0


