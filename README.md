# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Задание:
Необходимо создать свой пакет. 

Пакет должен отвечать PSR-4.

Пакет может подключаться в Composer либо с packagist, либо из GitHub.

# Подключение в проекте: 

1) Выполнить

```
composer require aovchinnikova/otus-php-hw3
```

2) Использование:

```
require __DIR__ . '/vendor/autoload.php';

use Aovchinnikova\OtusPhpHw3\HelloWorld;

$helloWorld = new HelloWorld();
echo $helloWorld->sayHello();
```
