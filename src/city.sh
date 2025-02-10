#!/bin/bash

# Проверяем, существует ли файл city
if [[ ! -f city.txt ]]; then
    echo "Файл city.txt не найден."
    exit 1
fi

# Используем awk для подсчета количества упоминаний каждого города
awk '{cities[$3]++} END {for (city in cities) print city, cities[city]}' city.txt | sort -k2,2nr | head -n 3