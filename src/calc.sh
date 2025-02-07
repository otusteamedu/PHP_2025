#!/bin/bash

calc() {
    
    if [[ "$1" =~ ^[+-]?[0-9]+$ ]] || [[ "$1" =~ ^[+-]?[0-9]+\.[0-9]+$ ]]; then
      result1=$(echo "$1" | grep -oE '[-]?[0-9]+\.?[0-9]*')
    else
      echo "\"$1\" не является числом."
      exit 1
    fi

    if [[ "$2" =~ ^[+-]?[0-9]+$ ]] || [[ "$2" =~ ^[+-]?[0-9]+\.[0-9]+$ ]]; then
      result2=$(echo "$2" | grep -oE '[-]?[0-9]+\.?[0-9]*')
    else
      echo "\"$2\" не является числом."
      exit 1
    fi

    sum=$(echo "$result1 + $result2" | bc)
    echo "Сумма: $sum"
    exit 0
    
}


calc $1 $2
 