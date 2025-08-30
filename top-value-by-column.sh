#!/usr/bin/env bash

TARGET_COLUMN=3;
SKIP_ROWS=1;
TOP=3

if [[ "$#" -ne 1 ]]; then
  echo "Usage: ./top-city.sh <input_file>";
  exit 1;
fi;

if [[ ! -s "$1" ]]; then
  echo "File $1 not exists or empty";
  exit 2;
fi;

COLUMNS_COUNT=$(awk 'NR==1 {print NF; exit}' "$1")
if [[ "TARGET_COLUMN" -ge "$COLUMNS_COUNT"  ]]; then
  echo "File must be contains minimum $TARGET_COLUMN columns";
  exit 3;
fi

awk -v c="$TARGET_COLUMN" -v r="$SKIP_ROWS" 'NR>$(r) {print $(c)}' "$1" | sort | uniq -ci | sort -nr | head "-$TOP";
exit 0;