#! /bin/bash
#
tail -n +2 cities.txt | sort -k 3 | awk '{print $3}' | uniq -c | sort -k 1 -r | head -n 3
