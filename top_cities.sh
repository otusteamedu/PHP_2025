TABLE_FILE="/app/users.txt"

if [ ! -f "$TABLE_FILE" ]; then
    echo "Ошибка: файл '$TABLE_FILE' не найден!"
    exit 1
fi

awk 'NR>1 {print $3}' "$TABLE_FILE" | sort | uniq -c | sort -nr | head -3
