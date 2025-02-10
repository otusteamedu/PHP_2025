#!/bin/bash
#checking if bc package present in the operating system
isBCinstalled=0
I=`dpkg -s bc | grep "installed" `
if [ -n "$I" ]
then
   isBCinstalled=1
else
   isBCinstalled=0
   echo "Package bc is not installed. Installing bc packege... "
   sudo apt install bc
   if [ $? -eq 0 ]
   then
      echo "bc packege successfully installed"
      isBCinstalled=1
   else
      echo "Error: package bc did not install"
      exit 1
   fi 
fi
#if bc package installed starting the calculator
if [ $isBCinstalled -eq 1  ]
 then
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
   sum=$(echo "$a + $b" | bc)
   echo Sum is $sum
   exit 0
fi


