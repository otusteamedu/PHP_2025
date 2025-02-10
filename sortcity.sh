#!/bin/bash
if [ -z "$1" ]; then
    file="city.txt"
else
    file="$1"
fi

if [[ ! -f "$file" ]] || [[ ! -r "$file" ]] || [[ ! -s "$file" ]]; then
    echo "Указан недоступный файл. Укажите верный: $0 <файл>"
    exit 1
fi

awk 'NR>1 {print $3}' $file | sort | uniq -c | sort -nr | head -n 3