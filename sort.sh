#!/bin/bash

city="$(dirname $0)/cities.txt"

grep -v '^$' $city | awk '{print $3}' | sort | uniq -c | sort -r | head --lines=3
