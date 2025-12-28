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

```sql
select * from movie_attributes_service_schedule;
```

# Выбор всех фильмов на сегодня

```sql
select
    movies.*
from movies
    inner join seances on movies.id = seances.movie_id
where
    seances.begin_at::date = now()::date
group by movies.id;
```

# Подсчёт проданных билетов за неделю

```sql
select
    tickets.*
from tickets
    inner join seances on seances.id = tickets.seance_id
where
    seances.begin_at >= date_trunc('week', now())
    and seances.begin_at <  date_trunc('week', now()) + INTERVAL '1 week';
```

# Формирование афиши (фильмы, которые показывают сегодня)

```sql
select
    movies.*,
    seances.begin_at,
    seances.end_at
from
    movies inner join seances on movies.id = seances.movie_id
where
    seances.begin_at::date = now()::date
order by movies.id;
```

# Поиск 3 самых прибыльных фильмов за неделю

```sql
select
    movies.title,
    sum(tickets.price) as total
from movies
    inner join seances on movies.id = seances.movie_id
    inner join tickets on seances.id = tickets.seance_id
where
    seances.begin_at >= date_trunc('week', now())
    and seances.begin_at <  date_trunc('week', now()) + INTERVAL '1 week'
group by movies.id
order by total desc;
```

# Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

```sql
select
    hall_seats.id,
    hall_seats.row,
    hall_seats.col,
    hall_seats.number,
    hall_seats.seat_type,
    case
        when tickets.id is null then 'free'
        else 'occupied'
        end as state
from seances
    inner join hall_seats on hall_seats.hall_id = seances.hall_id
    left join tickets on tickets.seance_id = seances.id and tickets.hall_seat_id = hall_seats.id
where seances.id = 1
order by hall_seats.row, hall_seats.col;
```

# Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс

```sql
select
    seances.*,
    min(tickets.price),
    max(tickets.price)
from seances
    inner join tickets on seances.id = tickets.seance_id
group by seances.id;
```

# 10k

```
Group  (cost=337.39..337.64 rows=50 width=18)
  Group Key: movies.id
  ->  Sort  (cost=337.39..337.52 rows=50 width=18)
        Sort Key: movies.id
        ->  Nested Loop  (cost=0.30..335.98 rows=50 width=18)
              ->  Seq Scan on seances  (cost=0.00..324.00 rows=50 width=8)
                    Filter: ((begin_at)::date = (now())::date)
              ->  Memoize  (cost=0.30..5.43 rows=1 width=18)
                    Cache Key: seances.movie_id
                    Cache Mode: logical
                    ->  Index Scan using movies_pk on movies  (cost=0.29..5.42 rows=1 width=18)
                          Index Cond: (id = seances.movie_id)
```

```
Hash Join  (cost=524.00..60414.57 rows=2997642 width=28)
  Hash Cond: (tickets.seance_id = seances.id)
  ->  Seq Scan on tickets  (cost=0.00..52018.42 rows=2997642 width=28)
  ->  Hash  (cost=399.00..399.00 rows=10000 width=8)
        ->  Seq Scan on seances  (cost=0.00..399.00 rows=10000 width=8)
               Filter: ((begin_at >= date_trunc('week'::text, now())) AND (begin_at < (date_trunc('week'::text, now()) + '7 days'::interval)))"
```

```
Sort  (cost=337.39..337.52 rows=50 width=34)
  Sort Key: movies.id
  ->  Nested Loop  (cost=0.30..335.98 rows=50 width=34)
        ->  Seq Scan on seances  (cost=0.00..324.00 rows=50 width=24)
              Filter: ((begin_at)::date = (now())::date)
        ->  Memoize  (cost=0.30..5.43 rows=1 width=18)
              Cache Key: seances.movie_id
              Cache Mode: logical
              ->  Index Scan using movies_pk on movies  (cost=0.29..5.42 rows=1 width=18)
                    Index Cond: (id = seances.movie_id)
```

```
Sort  (cost=52534.78..52559.78 rows=10000 width=26)
  Sort Key: (sum(tickets.price)) DESC
  ->  Finalize HashAggregate  (cost=51770.39..51870.39 rows=10000 width=26)
        Group Key: movies.id
        ->  Gather  (cost=49150.39..51650.39 rows=24000 width=26)
              Workers Planned: 2
              ->  Partial HashAggregate  (cost=48150.39..48250.39 rows=10000 width=26)
                    Group Key: movies.id
                    ->  Hash Join  (cost=813.00..41905.30 rows=1249018 width=22)
                          Hash Cond: (seances.movie_id = movies.id)
                          ->  Hash Join  (cost=524.00..38336.24 rows=1249018 width=12)
                                Hash Cond: (tickets.seance_id = seances.id)
                                ->  Parallel Seq Scan on tickets  (cost=0.00..34532.18 rows=1249018 width=12)
                                ->  Hash  (cost=399.00..399.00 rows=10000 width=16)
                                      ->  Seq Scan on seances  (cost=0.00..399.00 rows=10000 width=16)
                                             Filter: ((begin_at >= date_trunc('week'::text, now())) AND (begin_at < (date_trunc('week'::text, now()) + '7 days'::interval)))"
                          ->  Hash  (cost=164.00..164.00 rows=10000 width=18)
                                ->  Seq Scan on movies  (cost=0.00..164.00 rows=10000 width=18)
```

```
Sort  (cost=68.58..69.58 rows=400 width=61)
"  Sort Key: hall_seats.""row"", hall_seats.col"
  ->  Hash Left Join  (cost=20.12..51.29 rows=400 width=61)
        Hash Cond: (hall_seats.id = tickets.hall_seat_id)
        ->  Nested Loop  (cost=0.72..30.84 rows=400 width=37)
              ->  Index Scan using seances_pk on seances  (cost=0.29..8.30 rows=1 width=16)
                    Index Cond: (id = 1)
              ->  Index Scan using hall_seats_hall_id_index on hall_seats  (cost=0.43..18.50 rows=404 width=37)
                    Index Cond: (hall_id = seances.hall_id)
        ->  Hash  (cost=15.66..15.66 rows=299 width=24)
              ->  Index Scan using tickets_seance_id_index on tickets  (cost=0.43..15.66 rows=299 width=24)
                    Index Cond: (seance_id = 1)
```

```
Finalize HashAggregate  (cost=51208.87..51308.87 rows=10000 width=74)
  Group Key: seances.id
  ->  Gather  (cost=48528.87..51028.87 rows=24000 width=74)
        Workers Planned: 2
        ->  Partial HashAggregate  (cost=47528.87..47628.87 rows=10000 width=74)
              Group Key: seances.id
              ->  Hash Join  (cost=349.00..38161.24 rows=1249018 width=70)
                    Hash Cond: (tickets.seance_id = seances.id)
                    ->  Parallel Seq Scan on tickets  (cost=0.00..34532.18 rows=1249018 width=12)
                    ->  Hash  (cost=224.00..224.00 rows=10000 width=66)
                          ->  Seq Scan on seances  (cost=0.00..224.00 rows=10000 width=66)
```

# 100k

```
Group  (cost=3280.49..3282.99 rows=500 width=19)
  Group Key: movies.id
  ->  Sort  (cost=3280.49..3281.74 rows=500 width=19)
        Sort Key: movies.id
        ->  Nested Loop  (cost=0.30..3258.07 rows=500 width=19)
              ->  Seq Scan on seances  (cost=0.00..3235.00 rows=500 width=8)
                    Filter: ((begin_at)::date = (now())::date)
              ->  Memoize  (cost=0.30..5.30 rows=1 width=19)
                    Cache Key: seances.movie_id
                    Cache Mode: logical
                    ->  Index Scan using movies_pk on movies  (cost=0.29..5.29 rows=1 width=19)
                          Index Cond: (id = seances.movie_id)
```

```
Hash Join  (cost=4487.38..603825.59 rows=12056932 width=28)
  Hash Cond: (tickets.seance_id = seances.id)
  ->  Seq Scan on tickets  (cost=0.00..520585.32 rows=29999832 width=28)
  ->  Hash  (cost=3985.00..3985.00 rows=40190 width=8)
        ->  Seq Scan on seances  (cost=0.00..3985.00 rows=40190 width=8)
"              Filter: ((begin_at >= date_trunc('week'::text, now())) AND (begin_at < (date_trunc('week'::text, now()) + '7 days'::interval)))"
JIT:
  Functions: 12
"  Options: Inlining true, Optimization true, Expressions true, Deforming true"
```

```
Sort  (cost=3280.49..3281.74 rows=500 width=35)
  Sort Key: movies.id
  ->  Nested Loop  (cost=0.30..3258.07 rows=500 width=35)
        ->  Seq Scan on seances  (cost=0.00..3235.00 rows=500 width=24)
              Filter: ((begin_at)::date = (now())::date)
        ->  Memoize  (cost=0.30..5.30 rows=1 width=19)
              Cache Key: seances.movie_id
              Cache Mode: logical
              ->  Index Scan using movies_pk on movies  (cost=0.29..5.29 rows=1 width=19)
                    Index Cond: (id = seances.movie_id)
```

```
Sort  (cost=461640.42..461890.42 rows=100000 width=27)
  Sort Key: (sum(tickets.price)) DESC
  ->  Finalize HashAggregate  (cost=449941.60..450941.60 rows=100000 width=27)
        Group Key: movies.id
        ->  Gather  (cost=423741.60..448741.60 rows=240000 width=27)
              Workers Planned: 2
              ->  Partial HashAggregate  (cost=422741.60..423741.60 rows=100000 width=27)
                    Group Key: movies.id
                    ->  Hash Join  (cost=6035.16..397622.99 rows=5023722 width=23)
                          Hash Cond: (seances.movie_id = movies.id)
                          ->  Parallel Hash Join  (cost=3148.16..381548.17 rows=5023722 width=12)
                                Hash Cond: (tickets.seance_id = seances.id)
                                ->  Parallel Seq Scan on tickets  (cost=0.00..345586.30 rows=12499930 width=12)
                                ->  Parallel Hash  (cost=2852.65..2852.65 rows=23641 width=16)
                                      ->  Parallel Seq Scan on seances  (cost=0.00..2852.65 rows=23641 width=16)
"                                            Filter: ((begin_at >= date_trunc('week'::text, now())) AND (begin_at < (date_trunc('week'::text, now()) + '7 days'::interval)))"
                          ->  Hash  (cost=1637.00..1637.00 rows=100000 width=19)
                                ->  Seq Scan on movies  (cost=0.00..1637.00 rows=100000 width=19)
JIT:
  Functions: 25
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
```

```
Sort  (cost=420.78..421.78 rows=401 width=61)
"  Sort Key: hall_seats.""row"", hall_seats.col"
  ->  Nested Loop Left Join  (cost=1.28..403.44 rows=401 width=61)
        ->  Nested Loop  (cost=0.72..30.88 rows=401 width=37)
              ->  Index Scan using seances_pk on seances  (cost=0.29..8.31 rows=1 width=16)
                    Index Cond: (id = 1)
              ->  Index Scan using hall_seats_hall_id_index on hall_seats  (cost=0.43..18.52 rows=405 width=37)
                    Index Cond: (hall_id = seances.hall_id)
        ->  Index Scan using tickets_seance_id_hall_seat_id_uindex on tickets  (cost=0.56..0.92 rows=1 width=24)
              Index Cond: ((seance_id = 1) AND (hall_seat_id = hall_seats.id))
```

```
Finalize GroupAggregate  (cost=1000.74..1423645.48 rows=100000 width=74)
  Group Key: seances.id
  ->  Gather Merge  (cost=1000.74..1421370.48 rows=170000 width=74)
        Workers Planned: 1
        ->  Partial GroupAggregate  (cost=0.73..1401245.47 rows=100000 width=74)
              Group Key: seances.id
              ->  Nested Loop  (cost=0.73..1267893.27 rows=17646960 width=70)
                    ->  Parallel Index Scan using seances_pk on seances  (cost=0.29..3430.53 rows=58824 width=66)
                    ->  Index Scan using tickets_seance_id_index on tickets  (cost=0.44..17.74 rows=376 width=12)
                          Index Cond: (seance_id = seances.id)
JIT:
  Functions: 11
"  Options: Inlining true, Optimization true, Expressions true, Deforming true"
```

# После добавление индекса: optimize.sql

Добавил индекс на поле, потому что оно участвует в условиях:

```sql
create index seances_begin_at_index
    on seances (begin_at);
```

Использовать ```materialized view``` с обновлением хоть раз в секунду.

```
Group  (cost=3280.49..3282.99 rows=500 width=19)
  Group Key: movies.id
  ->  Sort  (cost=3280.49..3281.74 rows=500 width=19)
        Sort Key: movies.id
        ->  Nested Loop  (cost=0.30..3258.07 rows=500 width=19)
              ->  Seq Scan on seances  (cost=0.00..3235.00 rows=500 width=8)
                    Filter: ((begin_at)::date = (now())::date)
              ->  Memoize  (cost=0.30..5.30 rows=1 width=19)
                    Cache Key: seances.movie_id
                    Cache Mode: logical
                    ->  Index Scan using movies_pk on movies  (cost=0.29..5.29 rows=1 width=19)
                          Index Cond: (id = seances.movie_id)
```

```
Hash Join  (cost=1946.48..601284.70 rows=12056932 width=28)
  Hash Cond: (tickets.seance_id = seances.id)
  ->  Seq Scan on tickets  (cost=0.00..520585.32 rows=29999832 width=28)
  ->  Hash  (cost=1444.11..1444.11 rows=40190 width=8)
        ->  Index Scan using seances_begin_at_index on seances  (cost=0.30..1444.11 rows=40190 width=8)
"              Index Cond: ((begin_at >= date_trunc('week'::text, now())) AND (begin_at < (date_trunc('week'::text, now()) + '7 days'::interval)))"
JIT:
  Functions: 12
"  Options: Inlining true, Optimization true, Expressions true, Deforming true"
```

```
Sort  (cost=3280.49..3281.74 rows=500 width=35)
  Sort Key: movies.id
  ->  Nested Loop  (cost=0.30..3258.07 rows=500 width=35)
        ->  Seq Scan on seances  (cost=0.00..3235.00 rows=500 width=24)
              Filter: ((begin_at)::date = (now())::date)
        ->  Memoize  (cost=0.30..5.30 rows=1 width=19)
              Cache Key: seances.movie_id
              Cache Mode: logical
              ->  Index Scan using movies_pk on movies  (cost=0.29..5.29 rows=1 width=19)
                    Index Cond: (id = seances.movie_id)
```

```
Sort  (cost=460438.74..460688.74 rows=100000 width=27)
  Sort Key: (sum(tickets.price)) DESC
  ->  Finalize HashAggregate  (cost=448739.92..449739.92 rows=100000 width=27)
        Group Key: movies.id
        ->  Gather  (cost=422539.92..447539.92 rows=240000 width=27)
              Workers Planned: 2
              ->  Partial HashAggregate  (cost=421539.92..422539.92 rows=100000 width=27)
                    Group Key: movies.id
                    ->  Hash Join  (cost=4833.48..396421.31 rows=5023722 width=23)
                          Hash Cond: (seances.movie_id = movies.id)
                          ->  Hash Join  (cost=1946.48..380346.49 rows=5023722 width=12)
                                Hash Cond: (tickets.seance_id = seances.id)
                                ->  Parallel Seq Scan on tickets  (cost=0.00..345586.30 rows=12499930 width=12)
                                ->  Hash  (cost=1444.11..1444.11 rows=40190 width=16)
                                      ->  Index Scan using seances_begin_at_index on seances  (cost=0.30..1444.11 rows=40190 width=16)
"                                            Index Cond: ((begin_at >= date_trunc('week'::text, now())) AND (begin_at < (date_trunc('week'::text, now()) + '7 days'::interval)))"
                          ->  Hash  (cost=1637.00..1637.00 rows=100000 width=19)
                                ->  Seq Scan on movies  (cost=0.00..1637.00 rows=100000 width=19)
JIT:
  Functions: 25
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
```

```
Sort  (cost=420.78..421.78 rows=401 width=61)
"  Sort Key: hall_seats.""row"", hall_seats.col"
  ->  Nested Loop Left Join  (cost=1.28..403.44 rows=401 width=61)
        ->  Nested Loop  (cost=0.72..30.88 rows=401 width=37)
              ->  Index Scan using seances_pk on seances  (cost=0.29..8.31 rows=1 width=16)
                    Index Cond: (id = 1)
              ->  Index Scan using hall_seats_hall_id_index on hall_seats  (cost=0.43..18.52 rows=405 width=37)
                    Index Cond: (hall_id = seances.hall_id)
        ->  Index Scan using tickets_seance_id_hall_seat_id_uindex on tickets  (cost=0.56..0.92 rows=1 width=24)
              Index Cond: ((seance_id = 1) AND (hall_seat_id = hall_seats.id))
```

```
Finalize GroupAggregate  (cost=1000.74..1423645.48 rows=100000 width=74)
  Group Key: seances.id
  ->  Gather Merge  (cost=1000.74..1421370.48 rows=170000 width=74)
        Workers Planned: 1
        ->  Partial GroupAggregate  (cost=0.73..1401245.47 rows=100000 width=74)
              Group Key: seances.id
              ->  Nested Loop  (cost=0.73..1267893.27 rows=17646960 width=70)
                    ->  Parallel Index Scan using seances_pk on seances  (cost=0.29..3430.53 rows=58824 width=66)
                    ->  Index Scan using tickets_seance_id_index on tickets  (cost=0.44..17.74 rows=376 width=12)
                          Index Cond: (seance_id = seances.id)
JIT:
  Functions: 11
"  Options: Inlining true, Optimization true, Expressions true, Deforming true"
```

### Топ 15 самых больших объектов (таблицы + индексы)

| object\_name                    | total\_size | table\_size | indexes\_size |
|:--------------------------------|:------------|:------------|:--------------|
| tickets                         | 3937 MB     | 1723 MB     | 2213 MB       |
| hall\_seats                     | 672 MB      | 283 MB      | 390 MB        |
| seances                         | 22 MB       | 9880 kB     | 13 MB         |
| movies                          | 7344 kB     | 5096 kB     | 2248 kB       |
| halls                           | 792 kB      | 512 kB      | 280 kB        |
| movie\_entity\_attributes       | 64 kB       | 8192 bytes  | 56 kB         |
| movie\_entity\_attribute\_types | 48 kB       | 8192 bytes  | 40 kB         |
| movie\_entity\_values           | 40 kB       | 0 bytes     | 40 kB         |

### Топ 5 самых часто используемых индексов

| index\_name          | table\_name | idx\_scan | index\_size |
|:---------------------|:------------|:----------|:------------|
| seances\_pk          | seances     | 221553630 | 2208 kB     |
| hall\_seats\_pk      | hall\_seats | 221553499 | 86 MB       |
| halls\_pk            | halls       | 18328514  | 240 kB      |
| seances\_no\_overlap | seances     | 2340510   | 8272 kB     |
| movies\_pk           | movies      | 2320710   | 2208 kB     |

### Топ 5 самых редко используемых индексов

| index\_name                                                          | table\_name                     | idx\_scan | index\_size |
|:---------------------------------------------------------------------|:--------------------------------|:----------|:------------|
| movie\_entity\_attribute\_types\_type\_uindex                        | movie\_entity\_attribute\_types | 0         | 16 kB       |
| movie\_entity\_attributes\_movie\_entity\_attribute\_type\_id\_index | movie\_entity\_attributes       | 0         | 16 kB       |
| hall\_seats\_hall\_id\_row\_col\_uindex                              | hall\_seats                     | 0         | 121 MB      |
| seances\_movie\_id\_index                                            | seances                         | 0         | 648 kB      |
| movie\_entity\_attributes\_mode\_index                               | movie\_entity\_attributes       | 0         | 16 kB       |
