# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

```sql
select
    movies.title,
    sum(tickets.price) as total
from
    "movies" inner join seances on movies.id = seances.movie_id inner join prices on seances.id = prices.seance_id inner join tickets on prices.id = tickets.price_id
group by movies.id order by total desc;
```
