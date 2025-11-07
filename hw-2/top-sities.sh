#!/bin/bash

TOP_SITIES=$(awk 'NR>1 {print $3}' sities | sort | uniq -c | sort -nr | head -n 3 | awk '{print $2}')
echo "$TOP_SITIES"
