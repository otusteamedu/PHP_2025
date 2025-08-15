#!/usr/bin/bash

awk 'NR>1 {print $3}' "$1" | sort | uniq -c | sort -r | head --lines=3