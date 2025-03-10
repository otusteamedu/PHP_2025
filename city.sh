#!/bin/bash

if [[ ! -f table.txt ]]; then
    echo "Файл table.txt не найден."
    exit 1
fi

result=$(awk 'NR>1 {print $3}' table.txt | sort | uniq -c | sort -nr | head -n 3)
echo "$result"