#!/bin/bash 

TOP_CITIES=$(awk 'NR>1 {print $3}' cities.txt | sort | uniq -c | sort -nr | head -n 3 | awk '{print $2}')
echo "$TOP_CITIES"