#!/bin/bash

path_to_file="$pwd../table.txt"

if [ ! -f $path_to_file ]; then
  echo "'table.txt' file not found"
  exit 1
fi

echo `awk 'NR>1{print $3}' $path_to_file | sort | uniq -c | sort -nr -k 1 | awk '{print $2}' | head -n 3`
exit 0