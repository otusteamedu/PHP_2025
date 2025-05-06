#!/bin/bash

awk '{print $3}' phones.csv | sort | uniq -c | sort -nr | head -n 3 | awk '{print $2}'