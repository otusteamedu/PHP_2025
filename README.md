# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

**1. Docker**

1.1. Установить Docker себе на локальную машину

1.2. Описать инфраструктуру в Docker-compose, которая включает в себя

1.2.1. nginx (обрабатывает статику, пробрасывает выполнение скриптов в fpm)

1.2.2. php-fpm (соединяется с nginx через tcp-порт)

1.2.3. redis (соединяется с php по порту)

1.2.4. memcached (соединяется с php по порту)

1.2.5. БД соединяется по порту (не забудьте про директории с данными)

1.3 (Со звездочкой) Можно установить Composer

1.4 (Со звездочкой) Соединить FPM и Nginx через unix-сокет 