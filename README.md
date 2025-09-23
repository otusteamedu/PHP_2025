# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus


# Описание:
Реализован паттерн DataMapper с использованием IdentityLoad.
Для показа примера работы в БД создается таблица Movie, наполняется тестовыми данными и над ними проводятся различные операции

# Пример использования MovieMapper

Создание объекта MovieMapper
```php

$dsn = "pgsql:host=dbhost;port=dbport;dbname=dbname";
$pdo = new \PDO($dsn, $dbUser, $dbPass);
$movieMapper = new MovieMapper($pdo);


```

Добавление фильма в БД

```php
$movie = new Movie(
    'The Shawshank Redemption',
    'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
    new \DateTime('1994-09-23'),
    142,
    9.3
);
$movieMapper->save($movie);

```

Получение фильма из БД

```php

$movieMapper->getById(1);

```


Удаление фильма из БД

```php

$movieMapper->delete($movie);

```

Получение первых 10 фильмов из БД
```php
$moviesList = $this->movieMapper->getList(10, 0);

```