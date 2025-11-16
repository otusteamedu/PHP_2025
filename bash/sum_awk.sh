#! /bin/bash
if [[ $1 =~ ^-?[[:digit:]]+\.?[[:digit:]]*$ ]] && [[ $2 =~ ^-?[[:digit:]]+\.?[[:digit:]]*$ ]]; 
then awk "BEGIN {print $1 + $2}"
else echo "Invalid parameters!"
fi
