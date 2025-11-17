#!/bin/bash

cat ./data/table | \
    tail -n +2  | \
    awk '{print $3}' | \
    sort -k1 | \
    uniq -c | \
    sort -rk1 | \
    awk '{print $2}' | \
    head -n 3