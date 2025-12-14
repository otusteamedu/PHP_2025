# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

```sql
select
    movies.title,
    sum(tickets.price) as total
from
    movies inner join seances on movies.id = seances.movie_id inner join tickets on seances.id = tickets.seance_id
group by movies.id order by total desc;
```

# Все

```sql
select * from movie_attributes;
```

# Пользователи

```sql
select * from movie_attributes_public;
```

# Маркетинг

```sql
select * from movie_attributes_marketing;
```

# Служебные

```sql
select * from movie_attributes_service;
```
