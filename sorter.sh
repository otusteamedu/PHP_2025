#!/bin/bash

FILE=users.txt

if [ ! -f "$FILE" ]; then
    echo "File $FILE is not exists"
    exit 1
fi

if [ ! -s "$FILE" ]; then
    echo "File $FILE is empty"
    exit 1
fi


#sorting the file
awk 'NR>2' users.txt | awk '{print $3}' users.txt | sort | uniq -c | sort -nr | head -n 3