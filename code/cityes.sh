#!/bin/sh
DOC_DATA=$(tail -n +2 /data/otus.local/cityes.txt | sort -b -k3 | awk '{print $3}' | uniq -i| head -n 3)
echo "$DOC_DATA"