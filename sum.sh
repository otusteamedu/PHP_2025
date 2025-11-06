#!/usr/bin/env bash

A="$1"
B="$2"

REG='^-?[0-9]+([.][0-9]+)?$'

if ! command -v awk >/dev/null 2>&1; then
    echo "'awk' не установлен в системе" >&2
    exit 1
fi

if ! [[ $A =~ $REG ]]; then
    echo "$A - не число" >&2
    exit 1
fi

if ! [[ $B =~ $REG ]]; then
    echo "$B - не число" >&2
    exit 1
fi

echo $(echo "$A $B" | awk '{print $1 + $2}')
