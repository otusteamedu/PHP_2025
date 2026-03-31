#!/bin/sh

if ! [ -f "$1" ] ; then
  echo "Файл не существует '$1'"
  exit 1
fi
content=$(awk 'NR >= 2 { print }' < "$1")
result=$(echo "$content" | awk '{ print $3 }'| sort | uniq -c | sort -nr | head -n 3)

echo $result
exit 0
