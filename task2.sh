#!/bin/bash
echo "Топ-3 самых популярных городов:"
tail -n +2 ./cities_table.txt | awk '{print $3}' | sort | uniq -c | sort -nr | head -n 3 # | awk '{print $2}'
