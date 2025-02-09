#!/bin/bash

if [[ ! -f /data/cities.txt ]]; then
    echo "Файл cities.txt не найден."
    exit 1
fi

awk '{cities[$3]++} END {for (city in cities) print city, cities[city]}' /data/cities.txt | sort -k2,2nr | head -n 3

