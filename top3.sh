#!/bin/bash

if [ ! -f data.txt ]; then
  echo "Error: File data.txt does not exist."
  exit 1
fi

# top 3
awk '{if($3 != "") print $3}' data.txt | sort | uniq -c | sort -nr | head -n 3
