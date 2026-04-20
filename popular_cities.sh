#!/bin/env bash
awk '
    NF == 0 { next }
    NR == 1 { next }
    NF >= 3 { print $3 }
' input.txt | sort | uniq -c | sort -nr | head -n 3 | awk '{print $2}'
