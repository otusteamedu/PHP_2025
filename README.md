# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Описание

Спроектируйте схему данных для системы управления кинотеатром

* Кинотеатр имеет несколько залов, в каждом зале идет несколько разных сеансов, клиенты могут купить билеты на сеансы

* Спроектировать базу данных для управления кинотеатром

* Задокументировать с помощью логической модели

* Написать DDL скрипты

* Написать SQL для нахождения самого прибыльного фильма


Обратите внимание на то, что мы проектируем систему из реального мира. Попробуйте посмотреть на то, как устроена система покупки билета в кинотеатре.


1. Все ли сеансы и места стоят одинаково?

2. Как может выглядеть схема зала?


# Представление

Логическая модель для системы управления кинотеатром - PlantUML.png.
PlantUML сгенерирован с помощью бесплатного ресурса - https://www.plantuml.com/plantuml


# Использование

1. Разворот локальной базы данных

```docker run --name my-postgres -e POSTGRES_PASSWORD=mysecretpassword -d -p 5432:5432 postgres```
    
2. Скопируйте scripts с хоста в контейнер:

```docker cp scripts my-postgres:/scripts```

3. Подключение к контейнеру:

```docker exec -it my-postgres bash```

4. Подключение к PostgreSQL:

```psql -U postgres```

5. Создание базы данных:

```CREATE DATABASE cinema_db;```

6. Выход из postgres:

```\q```

7. Выполнение DDL-скрипта:

```psql -U postgres -d cinema_db -f /scripts/ddl.sql```

8. Выполнение DML-скрипта:

```psql -U postgres -d cinema_db -f /scripts/dml.sql```

9. Выполнение SQL для нахождения самого прибыльного фильма:

```psql -U postgres -d cinema_db -f /scripts/get_the_most_profitable_movie.sql```

P.S. Подключение к базе данных cinema_db:

```psql -h localhost -p 5432 -U postgres -d cinema_db```
