#!/bin/bash

users_file_name="users.csv"
users_file_abs_path="$(dirname "$(readlink -f "$0")")/${users_file_name}"

if [ ! -f "${users_file_abs_path}" ]; then
  echo "Файл ${users_file_abs_path} не найден"; exit 1
fi

awk '{ print $3 }' "${users_file_abs_path}" | sort | uniq -c | sed 's/^\s*//' | sort -r | head -n 3 | awk '{ print $2 }'
