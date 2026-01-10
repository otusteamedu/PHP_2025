#!/usr/bin/env bash

RANK_CITY_LIST="$(awk '{print $3}' < city.txt | grep -E '^[^city]' | sort | uniq -c | sort -nrk1 | head -n 3 | awk '{print $2}')"

echo -e "$RANK_CITY_LIST\n" | awk '{print $1}'
