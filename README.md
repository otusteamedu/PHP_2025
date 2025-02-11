# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# ДЗ для урока 2. Linux

**1. Написать консольное приложение (bash-скрипт), который принимает два числа и выводит их сумму в стандартный вывод.**

Например:

./sum.sh 1.5 -7

Если предоставлены неверные аргументы (для проверки на число можно использовать регулярное выражение) вывести ошибку в консоль.

Если Вы запускаете скрипты на базе Docker под Windows 10, то поведение функции sort по умолчанию отличается от стандартного в linux (числа сортируются как числа, а не как строки)

**2. Имеется таблица следующего вида:**

id user city phone

1 test Moscow 1234123

2 test2 Saint-P 1232121

3 test3 Tver 4352124

4 test4 Milan 7990923

5 test5 Moscow 908213

Таблица хранится в текстовом файле.

Вывести на экран 3 наиболее популярных города среди пользователей системы, используя утилиты Линукса.

Подсказка: рекомендуется использовать утилиты uniq, awk, sort, head.

Критерии оценки:

    Числа для суммирования могут быть отрицательными и вещественными.
    Пакет bc для дистрибутивов Linux не является пакетом по умолчанию. Либо предусмотрите момент, когда пакет не установлен, либо решите проблему при помощи других средств.

