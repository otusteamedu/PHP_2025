#!/bin/bash

# analyze_cities.sh - скрипт для анализа популярности городов

FILE=${1:-/data/users.txt}

echo "Топ-3 самых популярных городов из файла: $(basename "$FILE")"
echo "=========================================================="

# Основная команда анализа
awk 'NR>1 {print $3}' "$FILE" | sort | uniq -c | sort -rn | head -3