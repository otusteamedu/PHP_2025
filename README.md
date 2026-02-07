# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Запуск контейнеров сс пересборкой изменений
```shell
docker-compose up --build
```

# Запуск контейнеров

```shell
docker-compose up
```

# Удаление контейнеров

```shell
docker-compose down
```

# Поиск

```shell
php console.php --title 'рыцори' --category 'Фантастика' --price 2000 --inStock 1
```

**Warning**: для корректной работы обызательно задать хотя бы один из перечисленных параметров

**Note**: Параметр category может принимать значения
- Исторический роман
- Любовный роман
- Детектив
- Фантастика
- Сад и огород
- Детская литература
- Искусство
