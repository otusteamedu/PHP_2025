#!/bin/bash

# Проверка на наличие файла
FILE_PATH=${1:-user.txt}

if [[ ! -f "$FILE_PATH" ]]; 
then
  echo "Файл не найден: $FILE_PATH" >&2
  exit 1
fi

# Извлечение столбца по заголовку "City" (регистр неважен),
# подсчет уникальных и вывод трех самых популярных
awk '
  NR==1 {
    col=0;
    for (i=1; i<=NF; i++) {
      if (tolower($i) == "city") { 
        col=i; break 
      }
    }
    if (col==0) { 
      print "Не найден заголовок City" > "/dev/stderr"; 
      exit 1 
    }
    next
  }
  { print $col }
' "$FILE_PATH" | sort | uniq -c | sort -nr | head -n 3
