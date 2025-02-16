!#/bin/bash

file=$1

if [[ ! -f "$file" ]]; then
    echo "Ошибка: файл '$file' не найден."
    exit 1
fi

awk '{if ($3 != "") print $3}' $file | sort | uniq -c | sort -nr | head -n 3

