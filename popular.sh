#!/usr/bin/env bash

filepath=$1

if [[ -z "$filepath" ]]; then
    echo "FilePath param required"
    exit 1
fi 

if ! [[ -f "$filepath" && -s "$filepath" ]]; then
    echo "$filepath File doesn't exist or file is empty"
    exit 1
fi

awk 'NF > 0 && NR > 1 {print $3}' "$filepath" | sort | uniq -c | sort -nr | awk '{print $2}' | head -n 3