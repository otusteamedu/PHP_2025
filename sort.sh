#!/bin/bash

if [ ! -f ./test.txt ]; then
    echo "Файл не найден!"
    exit;
fi

awk '!/^$/' test.txt | awk '{print $3}' | sort | uniq -c | sort -r | head -3
