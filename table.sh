#!/bin/bash

filename="cities.txt"
echo "3 самых популярных города"
grep -v '^[[:space:]]*$' $filename | tail -n +2 | awk '{print $3}' | sort -n | uniq -c | sort -r | head -n 3
