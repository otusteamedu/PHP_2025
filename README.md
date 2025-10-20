# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Запросы
DDL запросы находятся в папке populate_data
Основные запросы к БД находятся в папке queries
1. [Выбор всех фильмов на сегодня](./queries/1_all_films.sql)
2. [Подсчёт проданных билетов за неделю](./queries/2_bookings_in_week.sql) 
3. [Формирование афиши (фильмы, которые показывают сегодня)](./queries/3_all_films_today.sql)
4. [Поиск 3 самых прибыльных фильмов за неделю](./queries/4_highest_grossing_films_in_week.sql)
5. [Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс](./queries/5_seats.sql)
6. [Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс](./queries/6_minmax_price.sql)

# Результаты для 10000 записей

## 1. [Выбор всех фильмов на сегодня](./queries/1_all_films.sql)
```
Unique  (cost=11.07..11.08 rows=1 width=56) (actual time=0.047..0.050 rows=8 loops=1)
  ->  Sort  (cost=11.07..11.07 rows=1 width=56) (actual time=0.046..0.047 rows=8 loops=1)
        Sort Key: movie.movie_id, movie.movie_name, movie.movie_description
        Sort Method: quicksort  Memory: 25kB
        ->  Nested Loop Left Join  (cost=0.28..11.06 rows=1 width=56) (actual time=0.016..0.038 rows=8 loops=1)
              ->  Seq Scan on session  (cost=0.00..2.75 rows=1 width=4) (actual time=0.009..0.016 rows=8 loops=1)
                    Filter: ((start_time)::date = CURRENT_DATE)
                    Rows Removed by Filter: 92
              ->  Index Scan using movie_pkey on movie  (cost=0.28..8.29 rows=1 width=56) (actual time=0.002..0.002 rows=1 loops=8)
                    Index Cond: (movie_id = session.movie_id)
Planning Time: 0.172 ms
Execution Time: 0.068 ms
```
## 2. [Подсчёт проданных билетов за неделю](./queries/2_bookings_in_week.sql) 

```
Aggregate  (cost=189.32..189.33 rows=1 width=8) (actual time=0.994..0.995 rows=1 loops=1)
  ->  Hash Right Join  (cost=4.01..189.08 rows=95 width=0) (actual time=0.091..0.991 rows=38 loops=1)
        Hash Cond: (booking.order_id = order.order_id)
        ->  Seq Scan on booking  (cost=0.00..159.06 rows=9506 width=4) (actual time=0.006..0.512 rows=9506 loops=1)
        ->  Hash  (cost=4.00..4.00 rows=1 width=4) (actual time=0.030..0.031 rows=5 loops=1)
              Buckets: 1024  Batches: 1  Memory Usage: 9kB
              ->  Seq Scan on order  (cost=0.00..4.00 rows=1 width=4) (actual time=0.017..0.028 rows=5 loops=1)
                    Filter: ((order_status_id = ANY ('{2,4}'::integer[])) AND ((created_at)::date <= now()) AND ((created_at)::date >= (now() - '7 days'::interval)))
                    Rows Removed by Filter: 95
Planning Time: 0.155 ms
Execution Time: 1.016 ms
```

## 3. [Формирование афиши (фильмы, которые показывают сегодня)](./queries/3_all_films_today.sql)

```
Sort  (cost=11.07..11.07 rows=1 width=64) (actual time=0.049..0.050 rows=8 loops=1)
  Sort Key: movie.movie_id
  Sort Method: quicksort  Memory: 25kB
  ->  Nested Loop Left Join  (cost=0.28..11.06 rows=1 width=64) (actual time=0.018..0.041 rows=8 loops=1)
        ->  Seq Scan on session  (cost=0.00..2.75 rows=1 width=12) (actual time=0.010..0.017 rows=8 loops=1)
              Filter: ((start_time)::date = CURRENT_DATE)
              Rows Removed by Filter: 92
        ->  Index Scan using movie_pkey on movie  (cost=0.28..8.29 rows=1 width=56) (actual time=0.003..0.003 rows=1 loops=8)
              Index Cond: (movie_id = session.movie_id)
Planning Time: 0.172 ms
Execution Time: 0.070 ms
```

## 4. [Поиск 3 самых прибыльных фильмов за неделю](./queries/4_highest_grossing_films_in_week.sql)

```
Limit  (cost=269.47..269.48 rows=3 width=57) (actual time=1.109..1.112 rows=3 loops=1)
  ->  Sort  (cost=269.47..271.97 rows=1000 width=57) (actual time=1.108..1.110 rows=3 loops=1)
        Sort Key: (sum(booking.booking_price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  HashAggregate  (cost=244.04..256.54 rows=1000 width=57) (actual time=1.089..1.100 rows=30 loops=1)
              Group Key: movie.movie_id
              Batches: 1  Memory Usage: 81kB
              ->  Hash Left Join  (cost=45.17..237.39 rows=1331 width=30) (actual time=0.283..1.074 rows=34 loops=1)
                    Hash Cond: (session.movie_id = movie.movie_id)
                    ->  Hash Left Join  (cost=6.67..195.38 rows=1331 width=9) (actual time=0.096..0.882 rows=34 loops=1)
                          Hash Cond: (booking.session_id = session.session_id)
                          ->  Hash Join  (cost=3.42..188.50 rows=1331 width=9) (actual time=0.077..0.858 rows=34 loops=1)
                                Hash Cond: (booking.order_id = order.order_id)
                                ->  Seq Scan on booking  (cost=0.00..159.06 rows=9506 width=13) (actual time=0.009..0.372 rows=9506 loops=1)
                                ->  Hash  (cost=3.25..3.25 rows=14 width=4) (actual time=0.019..0.020 rows=5 loops=1)
                                      Buckets: 1024  Batches: 1  Memory Usage: 9kB
                                      ->  Seq Scan on order  (cost=0.00..3.25 rows=14 width=4) (actual time=0.008..0.017 rows=5 loops=1)
                                            Filter: ((order_status_id = ANY ('{2,4}'::integer[])) AND ((created_at)::date >= (now() - '7 days'::interval)))
                                            Rows Removed by Filter: 95
                          ->  Hash  (cost=2.00..2.00 rows=100 width=8) (actual time=0.017..0.017 rows=100 loops=1)
                                Buckets: 1024  Batches: 1  Memory Usage: 12kB
                                ->  Seq Scan on session  (cost=0.00..2.00 rows=100 width=8) (actual time=0.002..0.009 rows=100 loops=1)
                    ->  Hash  (cost=26.00..26.00 rows=1000 width=25) (actual time=0.183..0.183 rows=1000 loops=1)
                          Buckets: 1024  Batches: 1  Memory Usage: 67kB
                          ->  Seq Scan on movie  (cost=0.00..26.00 rows=1000 width=25) (actual time=0.003..0.102 rows=1000 loops=1)
Planning Time: 0.387 ms
Execution Time: 1.146 ms
```

## 5. [Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс](./queries/5_seats.sql)

```
Hash Left Join  (cost=105.40..204.51 rows=1000 width=48) (actual time=0.223..0.651 rows=1000 loops=1)
  Hash Cond: (booking.order_id = order.order_id)
  ->  Hash Left Join  (cost=102.15..193.52 rows=1000 width=24) (actual time=0.197..0.491 rows=1000 loops=1)
        Hash Cond: (seat.seat_id = booking.seat_id)
        ->  Nested Loop  (cost=28.04..116.78 rows=1000 width=16) (actual time=0.067..0.237 rows=1000 loops=1)
              ->  Seq Scan on session  (cost=0.00..2.25 rows=1 width=8) (actual time=0.012..0.016 rows=1 loops=1)
                    Filter: (session_id = 23)
                    Rows Removed by Filter: 99
              ->  Bitmap Heap Scan on seat  (cost=28.04..104.53 rows=1000 width=16) (actual time=0.053..0.123 rows=1000 loops=1)
                    Recheck Cond: (room_id = session.room_id)
                    Heap Blocks: exact=7
                    ->  Bitmap Index Scan on seat_room_id_row_number_seat_number_key  (cost=0.00..27.79 rows=1000 width=0) (actual time=0.045..0.045 rows=1000 loops=1)
                          Index Cond: (room_id = session.room_id)
        ->  Hash  (cost=72.79..72.79 rows=106 width=16) (actual time=0.126..0.126 rows=106 loops=1)
              Buckets: 1024  Batches: 1  Memory Usage: 13kB
              ->  Bitmap Heap Scan on booking  (cost=5.11..72.79 rows=106 width=16) (actual time=0.020..0.112 rows=106 loops=1)
                    Recheck Cond: (session_id = 23)
                    Heap Blocks: exact=53
                    ->  Bitmap Index Scan on booking_session_id_seat_id_key  (cost=0.00..5.08 rows=106 width=0) (actual time=0.013..0.013 rows=106 loops=1)
                          Index Cond: (session_id = 23)
  ->  Hash  (cost=2.00..2.00 rows=100 width=8) (actual time=0.022..0.022 rows=100 loops=1)
        Buckets: 1024  Batches: 1  Memory Usage: 12kB
        ->  Seq Scan on order  (cost=0.00..2.00 rows=100 width=8) (actual time=0.004..0.012 rows=100 loops=1)
Planning Time: 0.280 ms
Execution Time: 0.706 ms
```

## 6. [Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс](./queries/6_minmax_price.sql)

```
Aggregate  (cost=73.32..73.33 rows=1 width=64) (actual time=0.225..0.226 rows=1 loops=1)
  ->  Bitmap Heap Scan on booking  (cost=5.11..72.79 rows=106 width=5) (actual time=0.059..0.200 rows=106 loops=1)
        Recheck Cond: (session_id = 23)
        Heap Blocks: exact=53
        ->  Bitmap Index Scan on booking_session_id_seat_id_key  (cost=0.00..5.08 rows=106 width=0) (actual time=0.044..0.044 rows=106 loops=1)
              Index Cond: (session_id = 23)
Planning Time: 0.181 ms
Execution Time: 0.264 ms
```


# Результаты для 10000000 без индексов

##  1. [Выбор всех фильмов на сегодня](./queries/1_all_films.sql)
```
Unique  (cost=2646.23..2651.23 rows=500 width=56) (actual time=10.820..11.709 rows=998 loops=1)
  ->  Sort  (cost=2646.23..2647.48 rows=500 width=56) (actual time=10.819..11.057 rows=7143 loops=1)
        Sort Key: movie.movie_id, movie.movie_name, movie.movie_description
        Sort Method: quicksort  Memory: 695kB
        ->  Hash Left Join  (cost=38.50..2623.81 rows=500 width=56) (actual time=0.232..9.413 rows=7143 loops=1)
              Hash Cond: (session.movie_id = movie.movie_id)
              ->  Seq Scan on session  (cost=0.00..2584.00 rows=500 width=4) (actual time=0.009..8.142 rows=7143 loops=1)
                    Filter: ((start_time)::date = CURRENT_DATE)
                    Rows Removed by Filter: 92857
              ->  Hash  (cost=26.00..26.00 rows=1000 width=56) (actual time=0.218..0.219 rows=1000 loops=1)
                    Buckets: 1024  Batches: 1  Memory Usage: 94kB
                    ->  Seq Scan on movie  (cost=0.00..26.00 rows=1000 width=56) (actual time=0.004..0.117 rows=1000 loops=1)
Planning Time: 0.162 ms
Execution Time: 11.758 ms
```

## 2. [Подсчёт проданных билетов за неделю](./queries/2_bookings_in_week.sql) 

```
Aggregate  (cost=184646.39..184646.40 rows=1 width=8) (actual time=1091.049..1091.053 rows=1 loops=1)
  ->  Hash Right Join  (cost=3771.10..184587.39 rows=23600 width=0) (actual time=17.349..1078.746 rows=331005 loops=1)
        Hash Cond: (booking.order_id = order.order_id)
        ->  Seq Scan on booking  (cost=0.00..155834.96 rows=9516296 width=4) (actual time=0.017..394.185 rows=9515691 loops=1)
        ->  Hash  (cost=3768.00..3768.00 rows=248 width=4) (actual time=17.281..17.283 rows=3205 loops=1)
              Buckets: 4096 (originally 1024)  Batches: 1 (originally 1)  Memory Usage: 145kB
              ->  Seq Scan on order  (cost=0.00..3768.00 rows=248 width=4) (actual time=3.499..16.973 rows=3205 loops=1)
                    Filter: ((order_status_id = ANY ('{2,4}'::integer[])) AND ((created_at)::date <= now()) AND ((created_at)::date >= (now() - '7 days'::interval)))
                    Rows Removed by Filter: 96795
Planning Time: 0.186 ms
JIT:
  Functions: 13
  Options: Inlining false, Optimization false, Expressions true, Deforming true
  Timing: Generation 0.527 ms (Deform 0.150 ms), Inlining 0.000 ms, Optimization 0.247 ms, Emission 3.272 ms, Total 4.046 ms
Execution Time: 1091.664 ms

```
## 3. [Формирование афиши (фильмы, которые показывают сегодня)](./queries/3_all_films_today.sql)

```
Sort  (cost=2646.23..2647.48 rows=500 width=64) (actual time=11.294..11.769 rows=7143 loops=1)
  Sort Key: movie.movie_id
  Sort Method: quicksort  Memory: 806kB
  ->  Hash Left Join  (cost=38.50..2623.81 rows=500 width=64) (actual time=0.536..10.045 rows=7143 loops=1)
        Hash Cond: (session.movie_id = movie.movie_id)
        ->  Seq Scan on session  (cost=0.00..2584.00 rows=500 width=12) (actual time=0.018..8.471 rows=7143 loops=1)
              Filter: ((start_time)::date = CURRENT_DATE)
              Rows Removed by Filter: 92857
        ->  Hash  (cost=26.00..26.00 rows=1000 width=56) (actual time=0.511..0.512 rows=1000 loops=1)
              Buckets: 1024  Batches: 1  Memory Usage: 94kB
              ->  Seq Scan on movie  (cost=0.00..26.00 rows=1000 width=56) (actual time=0.010..0.319 rows=1000 loops=1)
Planning Time: 0.333 ms
Execution Time: 12.165 ms
```


## 4. [Поиск 3 самых прибыльных фильмов за неделю](./queries/4_highest_grossing_films_in_week.sql)

```
Limit  (cost=125148.92..125148.93 rows=3 width=57) (actual time=579.477..595.104 rows=3 loops=1)
  ->  Sort  (cost=125148.92..125151.42 rows=1000 width=57) (actual time=571.379..587.005 rows=3 loops=1)
        Sort Key: (sum(booking.booking_price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  Finalize GroupAggregate  (cost=124875.14..125135.99 rows=1000 width=57) (actual time=569.473..586.825 rows=1000 loops=1)
              Group Key: movie.movie_id
              ->  Gather Merge  (cost=124875.14..125108.49 rows=2000 width=57) (actual time=569.451..585.714 rows=3000 loops=1)
                    Workers Planned: 2
                    Workers Launched: 2
                    ->  Sort  (cost=123875.12..123877.62 rows=1000 width=57) (actual time=553.908..553.966 rows=1000 loops=3)
                          Sort Key: movie.movie_id
                          Sort Method: quicksort  Memory: 134kB
                          Worker 0:  Sort Method: quicksort  Memory: 134kB
                          Worker 1:  Sort Method: quicksort  Memory: 134kB
                          ->  Partial HashAggregate  (cost=123812.79..123825.29 rows=1000 width=57) (actual time=553.374..553.651 rows=1000 loops=3)
                                Group Key: movie.movie_id
                                Batches: 1  Memory Usage: 577kB
                                Worker 0:  Batches: 1  Memory Usage: 577kB
                                Worker 1:  Batches: 1  Memory Usage: 577kB
                                ->  Hash Left Join  (cost=6347.36..120531.85 rows=656188 width=30) (actual time=61.246..525.273 rows=109385 loops=3)
                                      Hash Cond: (session.movie_id = movie.movie_id)
                                      ->  Hash Left Join  (cost=6308.86..118763.56 rows=656188 width=9) (actual time=60.866..503.273 rows=109385 loops=3)
                                            Hash Cond: (booking.session_id = session.session_id)
                                            ->  Hash Join  (cost=3224.86..113956.99 rows=656188 width=9) (actual time=25.850..420.900 rows=109385 loops=3)
                                                  Hash Cond: (booking.order_id = order.order_id)
                                                  ->  Parallel Seq Scan on booking  (cost=0.00..100323.23 rows=3965123 width=13) (actual time=0.019..151.704 rows=3171897 loops=3)
                                                  ->  Hash  (cost=3018.00..3018.00 rows=16549 width=4) (actual time=25.696..25.696 rows=3205 loops=3)
                                                        Buckets: 32768  Batches: 1  Memory Usage: 369kB
                                                        ->  Seq Scan on order  (cost=0.00..3018.00 rows=16549 width=4) (actual time=9.958..25.042 rows=3205 loops=3)
                                                              Filter: ((order_status_id = ANY ('{2,4}'::integer[])) AND ((created_at)::date >= (now() - '7 days'::interval)))
                                                              Rows Removed by Filter: 96795
                                            ->  Hash  (cost=1834.00..1834.00 rows=100000 width=8) (actual time=34.436..34.436 rows=100000 loops=3)
                                                  Buckets: 131072  Batches: 1  Memory Usage: 4931kB
                                                  ->  Seq Scan on session  (cost=0.00..1834.00 rows=100000 width=8) (actual time=0.050..9.994 rows=100000 loops=3)
                                      ->  Hash  (cost=26.00..26.00 rows=1000 width=25) (actual time=0.353..0.354 rows=1000 loops=3)
                                            Buckets: 1024  Batches: 1  Memory Usage: 67kB
                                            ->  Seq Scan on movie  (cost=0.00..26.00 rows=1000 width=25) (actual time=0.053..0.184 rows=1000 loops=3)
Planning Time: 0.376 ms
JIT:
  Functions: 100
  Options: Inlining false, Optimization false, Expressions true, Deforming true
  Timing: Generation 3.147 ms (Deform 1.106 ms), Inlining 0.000 ms, Optimization 1.207 ms, Emission 37.007 ms, Total 41.362 ms
Execution Time: 595.992 ms

```

## 5. [Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс](./queries/5_seats.sql)

```
Hash Right Join  (cost=882.18..1251.01 rows=1031 width=48) (actual time=1.672..3.103 rows=1000 loops=1)
  Hash Cond: (booking.seat_id = seat.seat_id)
  ->  Merge Right Join  (cost=378.49..741.80 rows=95 width=16) (actual time=0.218..1.535 rows=96 loops=1)
        Merge Cond: (order.order_id = booking.order_id)
        ->  Index Scan using order_pkey on order  (cost=0.29..3375.29 rows=100000 width=8) (actual time=0.007..0.914 rows=9914 loops=1)
        ->  Sort  (cost=378.20..378.44 rows=95 width=16) (actual time=0.199..0.202 rows=96 loops=1)
              Sort Key: booking.order_id
              Sort Method: quicksort  Memory: 28kB
              ->  Bitmap Heap Scan on booking  (cost=5.17..375.08 rows=95 width=16) (actual time=0.025..0.186 rows=96 loops=1)
                    Recheck Cond: (session_id = 688)
                    Heap Blocks: exact=96
                    ->  Bitmap Index Scan on booking_session_id_seat_id_key  (cost=0.00..5.15 rows=95 width=0) (actual time=0.013..0.013 rows=96 loops=1)
                          Index Cond: (session_id = 688)
  ->  Hash  (cost=490.80..490.80 rows=1031 width=16) (actual time=1.449..1.449 rows=1000 loops=1)
        Buckets: 2048  Batches: 1  Memory Usage: 63kB
        ->  Merge Join  (cost=8.76..490.80 rows=1031 width=16) (actual time=1.134..1.353 rows=1000 loops=1)
              Merge Cond: (seat.room_id = session.room_id)
              ->  Index Scan using seat_room_id_row_number_seat_number_key on seat  (cost=0.43..451397.47 rows=9998890 width=16) (actual time=0.007..0.855 rows=9001 loops=1)
              ->  Sort  (cost=8.32..8.33 rows=1 width=8) (actual time=0.050..0.050 rows=1 loops=1)
                    Sort Key: session.room_id
                    Sort Method: quicksort  Memory: 25kB
                    ->  Index Scan using session_pkey on session  (cost=0.29..8.31 rows=1 width=8) (actual time=0.012..0.013 rows=1 loops=1)
                          Index Cond: (session_id = 688)
Planning Time: 0.289 ms
Execution Time: 3.156 ms

```
## 6. [Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс](./queries/6_minmax_price.sql)

```
Aggregate  (cost=375.56..375.57 rows=1 width=64) (actual time=0.284..0.285 rows=1 loops=1)
  ->  Bitmap Heap Scan on booking  (cost=5.17..375.08 rows=95 width=5) (actual time=0.047..0.262 rows=107 loops=1)
        Recheck Cond: (session_id = 123)
        Heap Blocks: exact=107
        ->  Bitmap Index Scan on booking_session_id_seat_id_key  (cost=0.00..5.15 rows=95 width=0) (actual time=0.029..0.030 rows=107 loops=1)
              Index Cond: (session_id = 123)
Planning Time: 0.202 ms
Execution Time: 0.309 ms
```

# Созданные индексы

## Запрос 1
Индекс по времени начала сеанса, для ускорения поиска по дате начала сеанса 
```sql
create index idx_session_start_time on session ((start_time::date));
```
Индекс по Foreign Key для ускорения join
```sql
create index idx_session_movie_id on session (movie_id);
```
## Запрос 2
Индекс по времени создания заказа
```sql
create index idx_order_created_at on order ((created_at::date));
```
Индекс по Foreign Key для ускорения join
```sql
create index idx_booking_order_id on booking (order_id);
```

Частичный индекс по статусу заказа для заказов в статусе Оплачен и Выполнен для аналитических запросов
```sql
create index idx_order_active_status 
on order((created_at::date), order_status_id) 
where order_status_id in (2, 4);
```

### Запрос 3
Дополнительные индексы не создавались, подходят индексы для запроса 1

### Запрос 4
Индекс по цене бронирования
```sql
create index idx_booking_booking_priсe on booking (booking_price);
```
Индекс по Foreign Key для ускорения join
```sql
create index idx_booking_session_id on booking (session_id);
```

Составной индекс по бронированию с включением цены
```sql
create index idx_booking_session_order_include_booking_price ON booking(session_id, order_id) include (booking_price);
```

### Запрос 5

Индекс по Foreign Key для ускорения join
```sql
create index idx_seat_room_id on seat (room_id);
```
Индекс по паре Foreign Key для ускорения фильтрации
```sql
create index idx_booking_session_seat on booking (session_id, seat_id);
```

### Запрос 6
Индекс по цене бронирования создан выше




# Результаты для 10000000 с индексами


## 1. [Выбор всех фильмов на сегодня](./queries/1_all_films.sql)

```

Unique  (cost=834.40..839.40 rows=500 width=56) (actual time=4.593..5.457 rows=998 loops=1)
  ->  Sort  (cost=834.40..835.65 rows=500 width=56) (actual time=4.591..4.810 rows=7143 loops=1)
        Sort Key: movie.movie_id, movie.movie_name, movie.movie_description
        Sort Method: quicksort  Memory: 695kB
        ->  Hash Left Join  (cost=46.67..811.99 rows=500 width=56) (actual time=0.492..3.258 rows=7143 loops=1)
              Hash Cond: (session.movie_id = movie.movie_id)
              ->  Bitmap Heap Scan on session  (cost=8.17..772.17 rows=500 width=4) (actual time=0.260..1.952 rows=7143 loops=1)
                    Recheck Cond: ((start_time)::date = CURRENT_DATE)
                    Heap Blocks: exact=834
                    ->  Bitmap Index Scan on idx_session_start_time  (cost=0.00..8.05 rows=500 width=0) (actual time=0.195..0.196 rows=7143 loops=1)
                          Index Cond: ((start_time)::date = CURRENT_DATE)
              ->  Hash  (cost=26.00..26.00 rows=1000 width=56) (actual time=0.225..0.227 rows=1000 loops=1)
                    Buckets: 1024  Batches: 1  Memory Usage: 94kB
                    ->  Seq Scan on movie  (cost=0.00..26.00 rows=1000 width=56) (actual time=0.009..0.122 rows=1000 loops=1)
Planning Time: 0.377 ms
Execution Time: 5.522 ms

```

## 2. [Подсчёт проданных билетов за неделю](./queries/2_bookings_in_week.sql) 

```
Aggregate  (cost=8364.34..8364.35 rows=1 width=8) (actual time=50.605..50.607 rows=1 loops=1)
  ->  Nested Loop Left Join  (cost=7.27..8305.34 rows=23599 width=0) (actual time=0.219..39.850 rows=331005 loops=1)
        ->  Bitmap Heap Scan on order  (cost=6.84..531.39 rows=248 width=4) (actual time=0.210..1.387 rows=3205 loops=1)
              Recheck Cond: (((created_at)::date >= (now() - '7 days'::interval)) AND ((created_at)::date <= now()) AND (order_status_id = ANY ('{2,4}'::integer[])))
              Heap Blocks: exact=727
              ->  Bitmap Index Scan on idx_order_active_status  (cost=0.00..6.78 rows=248 width=0) (actual time=0.124..0.124 rows=3205 loops=1)
                    Index Cond: (((created_at)::date >= (now() - '7 days'::interval)) AND ((created_at)::date <= now()))
        ->  Index Only Scan using idx_booking_order_id on booking  (cost=0.43..21.91 rows=944 width=4) (actual time=0.001..0.006 rows=102 loops=3205)
              Index Cond: (order_id = order.order_id)
              Heap Fetches: 0
Planning Time: 0.356 ms
Execution Time: 50.640 ms

```

## 3. [Формирование афиши (фильмы, которые показывают сегодня)](./queries/3_all_films_today.sql)

```
Sort  (cost=834.40..835.65 rows=500 width=64) (actual time=4.392..4.767 rows=7143 loops=1)
  Sort Key: movie.movie_id
  Sort Method: quicksort  Memory: 806kB
  ->  Hash Left Join  (cost=46.67..811.99 rows=500 width=64) (actual time=0.518..3.372 rows=7143 loops=1)
        Hash Cond: (session.movie_id = movie.movie_id)
        ->  Bitmap Heap Scan on session  (cost=8.17..772.17 rows=500 width=12) (actual time=0.296..1.986 rows=7143 loops=1)
              Recheck Cond: ((start_time)::date = CURRENT_DATE)
              Heap Blocks: exact=834
              ->  Bitmap Index Scan on idx_session_start_time  (cost=0.00..8.05 rows=500 width=0) (actual time=0.234..0.234 rows=7143 loops=1)
                    Index Cond: ((start_time)::date = CURRENT_DATE)
        ->  Hash  (cost=26.00..26.00 rows=1000 width=56) (actual time=0.217..0.218 rows=1000 loops=1)
              Buckets: 1024  Batches: 1  Memory Usage: 94kB
              ->  Seq Scan on movie  (cost=0.00..26.00 rows=1000 width=56) (actual time=0.006..0.115 rows=1000 loops=1)
Planning Time: 0.465 ms
Execution Time: 5.198 ms

```

## 4. [Поиск 3 самых прибыльных фильмов за неделю](./queries/4_highest_grossing_films_in_week.sql)

```

Limit  (cost=123456.22..123456.23 rows=3 width=57) (actual time=551.638..565.228 rows=3 loops=1)
  ->  Sort  (cost=123456.22..123458.72 rows=1000 width=57) (actual time=541.795..555.385 rows=3 loops=1)
        Sort Key: (sum(booking.booking_price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  Finalize GroupAggregate  (cost=123182.44..123443.29 rows=1000 width=57) (actual time=540.341..555.207 rows=1000 loops=1)
              Group Key: movie.movie_id
              ->  Gather Merge  (cost=123182.44..123415.79 rows=2000 width=57) (actual time=540.322..554.373 rows=3000 loops=1)
                    Workers Planned: 2
                    Workers Launched: 2
                    ->  Sort  (cost=122182.42..122184.92 rows=1000 width=57) (actual time=524.256..524.302 rows=1000 loops=3)
                          Sort Key: movie.movie_id
                          Sort Method: quicksort  Memory: 134kB
                          Worker 0:  Sort Method: quicksort  Memory: 134kB
                          Worker 1:  Sort Method: quicksort  Memory: 134kB
                          ->  Partial HashAggregate  (cost=122120.09..122132.59 rows=1000 width=57) (actual time=523.717..524.022 rows=1000 loops=3)
                                Group Key: movie.movie_id
                                Batches: 1  Memory Usage: 577kB
                                Worker 0:  Batches: 1  Memory Usage: 577kB
                                Worker 1:  Batches: 1  Memory Usage: 577kB
                                ->  Hash Left Join  (cost=4658.26..118839.36 rows=656147 width=30) (actual time=34.170..496.115 rows=109385 loops=3)
                                      Hash Cond: (session.movie_id = movie.movie_id)
                                      ->  Hash Left Join  (cost=4619.76..117071.17 rows=656147 width=9) (actual time=33.822..474.814 rows=109385 loops=3)
                                            Hash Cond: (booking.session_id = session.session_id)
                                            ->  Hash Join  (cost=1535.76..112264.71 rows=656147 width=9) (actual time=9.559..403.133 rows=109385 loops=3)
                                                  Hash Cond: (booking.order_id = order.order_id)
                                                  ->  Parallel Seq Scan on booking  (cost=0.00..100320.71 rows=3964871 width=13) (actual time=0.018..151.733 rows=3171897 loops=3)
                                                  ->  Hash  (cost=1328.90..1328.90 rows=16549 width=4) (actual time=9.328..9.329 rows=3205 loops=3)
                                                        Buckets: 32768  Batches: 1  Memory Usage: 369kB
                                                        ->  Bitmap Heap Scan on order  (cost=188.55..1328.90 rows=16549 width=4) (actual time=6.986..8.851 rows=3205 loops=3)
                                                              Recheck Cond: (((created_at)::date >= (now() - '7 days'::interval)) AND (order_status_id = ANY ('{2,4}'::integer[])))
                                                              Heap Blocks: exact=727
                                                              ->  Bitmap Index Scan on idx_order_active_status  (cost=0.00..184.41 rows=16549 width=0) (actual time=6.887..6.887 rows=3205 loops=3)
                                                                    Index Cond: ((created_at)::date >= (now() - '7 days'::interval))
                                            ->  Hash  (cost=1834.00..1834.00 rows=100000 width=8) (actual time=23.531..23.531 rows=100000 loops=3)
                                                  Buckets: 131072  Batches: 1  Memory Usage: 4931kB
                                                  ->  Seq Scan on session  (cost=0.00..1834.00 rows=100000 width=8) (actual time=0.040..8.062 rows=100000 loops=3)
                                      ->  Hash  (cost=26.00..26.00 rows=1000 width=25) (actual time=0.305..0.305 rows=1000 loops=3)
                                            Buckets: 1024  Batches: 1  Memory Usage: 67kB
                                            ->  Seq Scan on movie  (cost=0.00..26.00 rows=1000 width=25) (actual time=0.045..0.155 rows=1000 loops=3)
Planning Time: 0.565 ms
JIT:
  Functions: 103
  Options: Inlining false, Optimization false, Expressions true, Deforming true
  Timing: Generation 3.818 ms (Deform 1.295 ms), Inlining 0.000 ms, Optimization 1.515 ms, Emission 28.756 ms, Total 34.089 ms
Execution Time: 566.589 ms
```

## 5. [Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс](./queries/5_seats.sql)

```

Hash Right Join  (cost=442.97..811.80 rows=1031 width=48) (actual time=0.706..2.267 rows=1000 loops=1)
  Hash Cond: (booking.seat_id = seat.seat_id)
  ->  Merge Right Join  (cost=378.49..741.80 rows=95 width=16) (actual time=0.345..1.781 rows=96 loops=1)
        Merge Cond: (order.order_id = booking.order_id)
        ->  Index Scan using order_pkey on order  (cost=0.29..3375.29 rows=100000 width=8) (actual time=0.007..1.022 rows=9914 loops=1)
        ->  Sort  (cost=378.20..378.44 rows=95 width=16) (actual time=0.324..0.328 rows=96 loops=1)
              Sort Key: booking.order_id
              Sort Method: quicksort  Memory: 28kB
              ->  Bitmap Heap Scan on booking  (cost=5.17..375.08 rows=95 width=16) (actual time=0.077..0.288 rows=96 loops=1)
                    Recheck Cond: (session_id = 688)
                    Heap Blocks: exact=96
                    ->  Bitmap Index Scan on idx_booking_session_seat  (cost=0.00..5.15 rows=95 width=0) (actual time=0.063..0.063 rows=96 loops=1)
                          Index Cond: (session_id = 688)
  ->  Hash  (cost=51.59..51.59 rows=1031 width=16) (actual time=0.335..0.336 rows=1000 loops=1)
        Buckets: 2048  Batches: 1  Memory Usage: 63kB
        ->  Nested Loop  (cost=0.73..51.59 rows=1031 width=16) (actual time=0.071..0.237 rows=1000 loops=1)
              ->  Index Scan using session_pkey on session  (cost=0.29..8.31 rows=1 width=8) (actual time=0.026..0.027 rows=1 loops=1)
                    Index Cond: (session_id = 688)
              ->  Index Scan using idx_seat_room_id on seat  (cost=0.43..32.79 rows=1049 width=16) (actual time=0.041..0.126 rows=1000 loops=1)
                    Index Cond: (room_id = session.room_id)
Planning Time: 0.595 ms
Execution Time: 2.323 ms
```

## 6. [Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс](./queries/6_minmax_price.sql)

```

Aggregate  (cost=10.57..10.58 rows=1 width=64) (actual time=0.060..0.060 rows=1 loops=1)
  ->  Index Only Scan using idx_booking_session_order_include_booking_price on booking  (cost=0.43..10.10 rows=95 width=5) (actual time=0.026..0.045 rows=107 loops=1)
        Index Cond: (session_id = 123)
        Heap Fetches: 0
Planning Time: 0.185 ms
Execution Time: 0.094 ms
```

# Сравнительная таблица

| 1\. [Выбор всех фильмов на сегодня](./queries/1_all_films.sql)                                                       | 11.07..11.08   | 2646.23..2651.23     | 834.40..839.40       |
| -------------------------------------------------------------------------------------------------------------------- | -------------- | -------------------- | -------------------- |
| 2\. [Подсчёт проданных билетов за неделю](./queries/2_bookings_in_week.sql)                                          | 189.32..189.33 | 184646.39..184646.40 | 8364.34..8364.35     |
| 3\. [Формирование афиши (фильмы, которые показывают сегодня)](./queries/3_all_films_today.sql)                       | 11.07..11.07   | 2646.23..2647.48     | 834.40..835.65       |
| 4\. [Поиск 3 самых прибыльных фильмов за неделю](./queries/4_highest_grossing_films_in_week.sql)                     | 269.47..269.48 | 125148.92..125148.93 | 123456.22..123456.23 |
| 5\. [Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс](./queries/5_seats.sql) | 105.40..204.51 | 882.18..1251.01      | 442.97..811.80       |
| 6\. [Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс](./queries/6_minmax_price.sql)     | 73.32..73.33   | 375.56..375.57       | 10.57..10.58         |


| Time                                                                                                                 | 10k                                                     | 10kk                                                                                                                                                                                                                                                                                             | 10kk с индексами                                                                                                                                                                                                                                                                                 |
| -------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| 1\. [Выбор всех фильмов на сегодня](./queries/1_all_films.sql)                                                       | Planning Time: 0.172 ms<br>Execution Time: 0.068 ms<br> | Planning Time: 0.162 ms<br>Execution Time: 11.758 ms                                                                                                                                                                                                                                             | Planning Time: 0.377 ms<br>Execution Time: 5.522 ms                                                                                                                                                                                                                                              |
| 2\. [Подсчёт проданных билетов за неделю](./queries/2_bookings_in_week.sql)                                          | Planning Time: 0.155 ms<br>Execution Time: 1.016 ms     | Planning Time: 0.186 ms<br>JIT:<br>Functions: 13<br>Options: Inlining false, Optimization false, Expressions true, Deforming true<br>Timing: Generation 0.527 ms (Deform 0.150 ms), Inlining 0.000 ms, Optimization 0.247 ms, Emission 3.272 ms, Total 4.046 ms<br>Execution Time: 1091.664 ms   | Planning Time: 0.356 ms<br>Execution Time: 50.640 ms                                                                                                                                                                                                                                             |
| 3\. [Формирование афиши (фильмы, которые показывают сегодня)](./queries/3_all_films_today.sql)                       | Planning Time: 0.172 ms<br>Execution Time: 0.070 ms<br> | Planning Time: 0.333 ms<br>Execution Time: 12.165 ms                                                                                                                                                                                                                                             | Planning Time: 0.465 ms<br>Execution Time: 5.198 ms                                                                                                                                                                                                                                              |
| 4\. [Поиск 3 самых прибыльных фильмов за неделю](./queries/4_highest_grossing_films_in_week.sql)                     | Planning Time: 0.387 ms<br>Execution Time: 1.146 ms<br> | Planning Time: 0.376 ms<br>JIT:<br>Functions: 100<br>Options: Inlining false, Optimization false, Expressions true, Deforming true<br>Timing: Generation 3.147 ms (Deform 1.106 ms), Inlining 0.000 ms, Optimization 1.207 ms, Emission 37.007 ms, Total 41.362 ms<br>Execution Time: 595.992 ms | Planning Time: 0.565 ms<br>JIT:<br>Functions: 103<br>Options: Inlining false, Optimization false, Expressions true, Deforming true<br>Timing: Generation 3.818 ms (Deform 1.295 ms), Inlining 0.000 ms, Optimization 1.515 ms, Emission 28.756 ms, Total 34.089 ms<br>Execution Time: 566.589 ms |
| 5\. [Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс](./queries/5_seats.sql) | Planning Time: 0.280 ms<br>Execution Time: 0.706 ms<br> | Planning Time: 0.289 ms<br>Execution Time: 3.156 ms                                                                                                                                                                                                                                              | Planning Time: 0.595 ms<br>Execution Time: 2.323 ms<br>                                                                                                                                                                                                                                          |
| 6\. [Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс](./queries/6_minmax_price.sql)     | Planning Time: 0.181 ms<br>Execution Time: 0.264 ms<br> | Planning Time: 0.202 ms<br>Execution Time: 0.309 ms<br>                                                                                                                                                                                                                                          | Planning Time: 0.185 ms<br>Execution Time: 0.094 ms<br>                                                                                                                                                                                                                                          |

## Выводы
Удалось оптимизировать 5 запросов из 6 с помощью индексов.

|                                                                                                                      | Оптимизация Cost | Оптимизация Time               |
| -------------------------------------------------------------------------------------------------------------------- | ---------------- | ------------------------------ |
| 1\. [Выбор всех фильмов на сегодня](./queries/1_all_films.sql)                                                       | x3.17            | x2.13                          |
| 2\. [Подсчёт проданных билетов за неделю](./queries/2_bookings_in_week.sql)                                          | x22              | x21.8                          |
| 3\. [Формирование афиши (фильмы, которые показывают сегодня)](./queries/3_all_films_today.sql)                       | x3.17            | x2.34                          |
| 4\. [Поиск 3 самых прибыльных фильмов за неделю](./queries/4_highest_grossing_films_in_week.sql)                     | x1.01            | x1.05 (в пределах погрешности) |
| 5\. [Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс](./queries/5_seats.sql) | x1.51            | x1.34                          |
| 6\. [Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс](./queries/6_minmax_price.sql)     | x37.5            | x3.28                          |

## Размеры 15 самых больших таблиц и индексов

| relname                                         | size    | table_size |
| ----------------------------------------------- | ------- | ---------- |
| seat                                            | 498 MB  | 522190848  |
| booking                                         | 474 MB  | 497025024  |
| seat_room_id_row_number_seat_number_key         | 301 MB  | 315400192  |
| idx_booking_session_order_include_booking_price | 286 MB  | 299745280  |
| seat_pkey                                       | 214 MB  | 224641024  |
| idx_booking_booking_priсe                       | 204 MB  | 214065152  |
| booking_session_id_seat_id_key                  | 204 MB  | 213762048  |
| idx_booking_session_seat                        | 204 MB  | 213762048  |
| booking_pkey                                    | 204 MB  | 213762048  |
| idx_seat_room_id                                | 70 MB   | 73187328   |
| idx_booking_order_id                            | 66 MB   | 69582848   |
| idx_booking_session_id                          | 64 MB   | 67592192   |
| session                                         | 6672 kB | 6832128    |
| order                                           | 6144 kB | 6291456    |
| order_pkey                                      | 2208 kB | 2260992    |

## Самые часто используемые Индексы

| relname | indexrelname             | idx_scan | idx_tup_read | idx_tup_fetch |
| ------- | ------------------------ | -------- | ------------ | ------------- |
| booking | idx_booking_order_id     | 51334    | 5250534      | 0             |
| order   | idx_order_active_status  | 34       | 108970       | 0             |
| session | idx_session_movie_id     | 24       | 24           | 0             |
| booking | idx_booking_session_seat | 17       | 492          | 0             |
| seat    | idx_seat_room_id         | 15       | 5010         | 5000          |

## Самые редко используемые индексы
| relname | indexrelname                                    | idx_scan | idx_tup_read | idx_tup_fetch |
| ------- | ----------------------------------------------- | -------- | ------------ | ------------- |
| booking | idx_booking_booking_priсe                       | 0        | 0            | 0             |
| order   | idx_order_created_at                            | 0        | 0            | 0             |
| booking | idx_booking_session_id                          | 0        | 0            | 0             |
| booking | idx_booking_session_order_include_booking_price | 4        | 428          | 0             |
| session | idx_session_start_time                          | 6        | 42858        | 0             |