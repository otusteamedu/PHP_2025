#!/bin/bash
set -euo pipefail

TEMPLATE="${1:-deploy/env.template}"
OUTPUT="${2:-$DEPLOY_DIR/.env}"

# Извлекаем имена ${VAR} из шаблона для envsubst
VARS=$(grep -oP '\$\{(\w+)\}' "$TEMPLATE" | sed 's/${//;s/}//' | sort -u | tr '\n' ' ')

if [ -z "$VARS" ]; then
    echo "ERROR: No variables found in template $TEMPLATE" >&2
    exit 1
fi

echo "# Auto-generated from $TEMPLATE via envsubst — $(date -Iseconds)" > "$OUTPUT"
envsubst "$VARS" < "$TEMPLATE" >> "$OUTPUT"

echo "Rendered $OUTPUT with variables: $VARS"
