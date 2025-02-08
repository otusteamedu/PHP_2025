#!/bin/bash

awk 'NR>1 {print $3}' table_data.txt | sort | uniq -c | sort -nr | head -n 3 | awk '{print $2}';
