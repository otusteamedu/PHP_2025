#!/usr/bin/env bash

set -euo pipefail

TABLE_FILE="${1:-table.txt}"

if [[ ! -f "$TABLE_FILE" ]]; then
  echo "Error: file not found: $TABLE_FILE" >&2
  exit 1
fi

awk 'NR>1 && $3 != "" { print $3 }' "$TABLE_FILE" \
  | sort \
  | uniq -c \
  | sort -nr \
  | head -n 3 \
  | awk '{ print $2 }'
