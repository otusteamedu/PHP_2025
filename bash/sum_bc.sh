#! /bin/bash
if ! dpkg -s bcyy &>/dev/null; then
 apt install bc
 echo "Packet installed"
fi

if [[ $1 =~ ^-?[[:digit:]]+\.?[[:digit:]]$ ]] && [[ $2 =~ ^-?[[:digit:]]+\.?[[:digit:]]*$ ]]; 
then echo "scale=3;$1+$2" | bc
else echo "Invalid parameters!"
fi
