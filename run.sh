#!/bin/bash

CURRENT=$(docker ps --format '{{.Names}}' | grep -E '^blue$|^green$')

if [ -z "$CURRENT" ]; then
    echo "Application is not running"
    exit 1
fi

docker exec "$CURRENT" php /app/entry_point/app.php "$@"