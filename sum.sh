if [[ $# -ne 2 ]]; then
    echo "Ошибка: необходимо передать два числа."
    exit 1
fi

regex='^-?[0-9]+([.][0-9]+)?$'

if ! [[ $1 =~ $regex && $2 =~ $regex ]]; then
    echo "Ошибка: аргументы должны быть числами."
    exit 1
fi

sum=$(awk "BEGIN {print $1 + $2}")

echo "Сумма: $sum"
