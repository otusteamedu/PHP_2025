!#/bin/bash

file=$1

awk '{print $3}' $file | sort | uniq -c | sort -nr | head -n 3

