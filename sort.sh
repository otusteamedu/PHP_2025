#!/bin/bash

if (( BASH_VERSINFO[0] < 4 )); then
    echo "Требуется bash 4.0 или новее, ваша версия: " $BASH_VERSION
    exit 1
fi

if [ $# -eq 0 ]; then
    echo "Вы не указали файл для обработки"
    exit 1
fi

filename="$1"

if [ ! -f "$filename" ]; then
    echo "Файл '$filename' не существует"
    exit 1
fi

IFS=$' \t' read -r -a headers < <(head -n 1 "$filename")

for header_key in "${!headers[@]}"; do
    declare -a "col_$header"
done



readarray -t lines < <(grep -v "^$" $filename)
unset lines[0]
declare -A result

for line_key in "${!lines[@]}"; do
    declare -a words
    read -a words <<< ${lines[$line_key]}
    for word_key in "${!words[@]}"; do
        result["${headers[$word_key]}"]+="${words[$word_key]}"$'\n'
    done
done
echo "${result[city]}" | sort | uniq -c | sort -nr | head -3
