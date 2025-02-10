#!/bin/bash

if [ -z  "$1" ]; then
    echo "Enter: $0 <file name>"
    exit 1
fi

file=$1

if [ ! -f "$file" ]; then
    echo "File '$file' does not exist"
    exit 1
elif [ ! -r "$file" ]; then
    echo "File is unavailable"
    exit 1
elif [ ! -s "$file" ]; then
    echo "File is empty"
    exit 1
fi

echo "3 popular cities among users of the system $file:"
echo ""
awk 'NR > 1 && NF {print $3}' "$file" | sort | uniq -c | sort -nr | head -n 3