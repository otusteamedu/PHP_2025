#!/bin/bash

if [ $# -ne 2 ]
then
  echo "Ожидается два аргумента"
  exit 1
fi

OPERAND_1=$1
OPERAND_2=$2

function isNumber() {
  re='^-?[0-9]+([.][0-9]+)?$'
  [[ $1 =~ $re ]]
}

if ! isNumber "$OPERAND_1";
then
  echo "Первый операнд не является числом"
  exit 1
fi

if ! isNumber "$OPERAND_2";
then
  echo "Второй операнд не является числом"
  exit 1
fi

function installBc() {
 if ! command -v bc >/dev/null 2>&1; then
   echo "bc не найден, устанавливаю..."
   if [ -f /etc/alpine-release ]; then
       echo "Обнаружен Alpine Linux"
       apk add --no-cache bc || { echo "Ошибка установки bc"; exit 1; }

   elif [ -f /etc/debian_version ]; then
       echo "Обнаружен Debian/Ubuntu"
       apt-get update && apt-get install -y bc || { echo "Ошибка установки bc"; exit 1; }

   elif [ -f /etc/arch-release ]; then
       echo "Обнаружен Arch Linux"
       pacman -Sy --noconfirm bc || { echo "Ошибка установки bc"; exit 1; }

   else
       echo "Неизвестный дистрибутив — не могу установить dc автоматически"
       exit 1
   fi
 fi
}

installBc

echo "$1 + $2" | bc