# До 1 000 000 записей

## 1 запрос – Выбор всех фильмов на сегодня

```sql
EXPLAIN ANALYZE
SELECT DISTINCT m.title, m.description, s.start_time, m.duration_minutes
FROM movies m
    JOIN sessions s ON m.id = s.movie_id
WHERE start_time >= CURRENT_DATE AND start_time < CURRENT_DATE + INTERVAL '1 day';
```

### без индексов:
```sql
-- HashAggregate  (cost=6122.35..6161.56 rows=3921 width=57) (actual time=33.424..34.343 rows=6671 loops=1)
-- "  Group Key: m.title, m.description, s.start_time, m.duration_minutes"
--   Batches: 1  Memory Usage: 1425kB
--   ->  Gather  (cost=5651.83..6083.14 rows=3921 width=57) (actual time=30.433..31.465 rows=6671 loops=1)
--         Workers Planned: 1
--         Workers Launched: 1
--         ->  HashAggregate  (cost=4651.83..4691.04 rows=3921 width=57) (actual time=25.762..26.250 rows=3336 loops=2)
-- "              Group Key: m.title, m.description, s.start_time, m.duration_minutes"
--               Batches: 1  Memory Usage: 1233kB
--               Worker 0:  Batches: 1  Memory Usage: 721kB
--               ->  Hash Join  (cost=85.25..4612.62 rows=3921 width=57) (actual time=1.243..24.072 rows=3336 loops=2)
--                     Hash Cond: (s.movie_id = m.id)
--                     ->  Parallel Seq Scan on sessions s  (cost=0.00..4517.06 rows=3921 width=12) (actual time=0.020..21.959 rows=3336 loops=2)
--                           Filter: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
--                           Rows Removed by Filter: 96664
--                     ->  Hash  (cost=54.00..54.00 rows=2500 width=53) (actual time=1.181..1.182 rows=2500 loops=2)
--                           Buckets: 4096  Batches: 1  Memory Usage: 247kB
--                           ->  Seq Scan on movies m  (cost=0.00..54.00 rows=2500 width=53) (actual time=0.172..0.630 rows=2500 loops=2)
-- Planning Time: 0.271 ms
-- Execution Time: 34.797 ms
```
### добавлен индекс:
```sql
CREATE INDEX idx_sessions_date ON sessions(start_time);
```
```sql
-- HashAggregate  (cost=2280.94..2347.59 rows=6665 width=57) (actual time=12.436..13.970 rows=6671 loops=1)
-- "  Group Key: m.title, m.description, s.start_time, m.duration_minutes"
--   Batches: 1  Memory Usage: 1425kB
--   ->  Hash Join  (cost=176.79..2214.29 rows=6665 width=57) (actual time=2.987..9.077 rows=6671 loops=1)
--         Hash Cond: (s.movie_id = m.id)
--         ->  Bitmap Heap Scan on sessions s  (cost=91.54..2111.51 rows=6665 width=12) (actual time=1.410..5.210 rows=6671 loops=1)
--               Recheck Cond: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
--               Heap Blocks: exact=1823
--               ->  Bitmap Index Scan on idx_sessions_date  (cost=0.00..89.88 rows=6665 width=0) (actual time=1.149..1.149 rows=6671 loops=1)
--                     Index Cond: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
--         ->  Hash  (cost=54.00..54.00 rows=2500 width=53) (actual time=1.534..1.535 rows=2500 loops=1)
--               Buckets: 4096  Batches: 1  Memory Usage: 247kB
--               ->  Seq Scan on movies m  (cost=0.00..54.00 rows=2500 width=53) (actual time=0.040..0.757 rows=2500 loops=1)
-- Planning Time: 1.174 ms
-- Execution Time: 15.293 ms
```

## 2 запрос – Подсчёт проданных билетов за неделю

```sql
EXPLAIN ANALYZE
SELECT COUNT(*) AS total_tickets
FROM tickets
WHERE purchase_time >= NOW() - INTERVAL '7 days';
```

### без индексов:
```sql
-- Finalize Aggregate  (cost=28989.55..28989.56 rows=1 width=8) (actual time=127.605..128.705 rows=1 loops=1)
--   ->  Gather  (cost=28989.33..28989.54 rows=2 width=8) (actual time=127.534..128.701 rows=3 loops=1)
--         Workers Planned: 2
--         Workers Launched: 2
--         ->  Partial Aggregate  (cost=27989.33..27989.34 rows=1 width=8) (actual time=121.659..121.660 rows=1 loops=3)
--               ->  Parallel Seq Scan on tickets  (cost=0.00..26947.67 rows=416667 width=0) (actual time=24.180..111.112 rows=333333 loops=3)
--                     Filter: (purchase_time >= (now() - '7 days'::interval))
-- Planning Time: 0.677 ms
-- Execution Time: 128.753 ms
```

### добавлен индекс:
```sql
CREATE INDEX idx_tickets_purchase_time ON tickets(purchase_time);
```
```sql
-- Finalize Aggregate  (cost=14727.78..14727.79 rows=1 width=8) (actual time=48.820..50.080 rows=1 loops=1)
--   ->  Gather  (cost=14727.56..14727.77 rows=2 width=8) (actual time=48.677..50.073 rows=3 loops=1)
--         Workers Planned: 2
--         Workers Launched: 2
--         ->  Partial Aggregate  (cost=13727.56..13727.57 rows=1 width=8) (actual time=41.730..41.730 rows=1 loops=3)
--               ->  Parallel Index Only Scan using idx_tickets_purchase_time on tickets  (cost=0.43..12685.90 rows=416667 width=0) (actual time=0.067..25.361 rows=333333 loops=3)
--                     Index Cond: (purchase_time >= (now() - '7 days'::interval))
--                     Heap Fetches: 0
-- Planning Time: 0.131 ms
-- Execution Time: 50.843 ms
```

## 3 запрос – Формирование афиши (фильмы, которые показывают сегодня)

```sql
EXPLAIN ANALYZE
SELECT m.title AS movie, s.start_time AS showtime, h.title AS hall
FROM movies m
    JOIN sessions s ON m.id = s.movie_id
    JOIN halls h ON s.hall_id = h.id
WHERE start_time >= CURRENT_DATE AND start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.start_time;
```

### без индексов:
```sql
-- Gather Merge  (cost=5872.56..6323.47 rows=3921 width=30) (actual time=34.924..37.128 rows=6671 loops=1)
--   Workers Planned: 1
--   Workers Launched: 1
--   ->  Sort  (cost=4872.55..4882.35 rows=3921 width=30) (actual time=28.448..28.602 rows=3336 loops=2)
--         Sort Key: s.start_time
--         Sort Method: quicksort  Memory: 304kB
--         Worker 0:  Sort Method: quicksort  Memory: 254kB
--         ->  Hash Join  (cost=86.32..4638.52 rows=3921 width=30) (actual time=1.449..27.300 rows=3336 loops=2)
--               Hash Cond: (s.hall_id = h.id)
--               ->  Hash Join  (cost=85.25..4612.62 rows=3921 width=25) (actual time=1.276..26.497 rows=3336 loops=2)
--                     Hash Cond: (s.movie_id = m.id)
--                     ->  Parallel Seq Scan on sessions s  (cost=0.00..4517.06 rows=3921 width=16) (actual time=0.049..24.174 rows=3336 loops=2)
--                           Filter: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
--                           Rows Removed by Filter: 96664
--                     ->  Hash  (cost=54.00..54.00 rows=2500 width=17) (actual time=1.198..1.199 rows=2500 loops=2)
--                           Buckets: 4096  Batches: 1  Memory Usage: 159kB
--                           ->  Seq Scan on movies m  (cost=0.00..54.00 rows=2500 width=17) (actual time=0.152..0.694 rows=2500 loops=2)
--               ->  Hash  (cost=1.03..1.03 rows=3 width=13) (actual time=0.100..0.100 rows=3 loops=2)
--                     Buckets: 1024  Batches: 1  Memory Usage: 9kB
--                     ->  Seq Scan on halls h  (cost=0.00..1.03 rows=3 width=13) (actual time=0.070..0.072 rows=3 loops=2)
-- Planning Time: 1.766 ms
-- Execution Time: 37.945 ms
```

### используем ранее добавленный индекс:
```sql
CREATE INDEX idx_sessions_date ON sessions(start_time);
```
```sql
-- Sort  (cost=2680.88..2697.54 rows=6665 width=30) (actual time=12.271..12.986 rows=6671 loops=1)
--   Sort Key: s.start_time
--   Sort Method: quicksort  Memory: 557kB
--   ->  Hash Join  (cost=177.86..2257.57 rows=6665 width=30) (actual time=2.597..10.231 rows=6671 loops=1)
--         Hash Cond: (s.hall_id = h.id)
--         ->  Hash Join  (cost=176.79..2214.29 rows=6665 width=25) (actual time=2.569..8.485 rows=6671 loops=1)
--               Hash Cond: (s.movie_id = m.id)
--               ->  Bitmap Heap Scan on sessions s  (cost=91.54..2111.51 rows=6665 width=16) (actual time=1.563..5.148 rows=6671 loops=1)
--                     Recheck Cond: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
--                     Heap Blocks: exact=1823
--                     ->  Bitmap Index Scan on idx_sessions_date  (cost=0.00..89.88 rows=6665 width=0) (actual time=1.306..1.307 rows=6671 loops=1)
--                           Index Cond: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
--               ->  Hash  (cost=54.00..54.00 rows=2500 width=17) (actual time=0.982..0.983 rows=2500 loops=1)
--                     Buckets: 4096  Batches: 1  Memory Usage: 159kB
--                     ->  Seq Scan on movies m  (cost=0.00..54.00 rows=2500 width=17) (actual time=0.011..0.547 rows=2500 loops=1)
--         ->  Hash  (cost=1.03..1.03 rows=3 width=13) (actual time=0.014..0.015 rows=3 loops=1)
--               Buckets: 1024  Batches: 1  Memory Usage: 9kB
--               ->  Seq Scan on halls h  (cost=0.00..1.03 rows=3 width=13) (actual time=0.008..0.009 rows=3 loops=1)
-- Planning Time: 1.037 ms
-- Execution Time: 13.397 ms
```

## 4 запрос – Поиск 3 самых прибыльных фильмов за неделю

```sql
EXPLAIN ANALYZE
SELECT m.title, SUM(t.price) AS total_revenue
FROM tickets t
    JOIN sessions s ON t.session_id = s.id
    JOIN movies m ON s.movie_id = m.id
WHERE t.purchase_time >= NOW() - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
    LIMIT 3;
```

### без индексов:
```sql
-- Limit  (cost=37679.50..37679.51 rows=3 width=49) (actual time=225.848..227.062 rows=3 loops=1)
--   ->  Sort  (cost=37679.50..37685.75 rows=2500 width=49) (actual time=225.848..227.061 rows=3 loops=1)
--         Sort Key: (sum(t.price)) DESC
--         Sort Method: top-N heapsort  Memory: 25kB
--         ->  Finalize GroupAggregate  (cost=36995.06..37647.19 rows=2500 width=49) (actual time=225.776..227.037 rows=50 loops=1)
--               Group Key: m.id
--               ->  Gather Merge  (cost=36995.06..37578.44 rows=5000 width=49) (actual time=225.762..226.987 rows=150 loops=1)
--                     Workers Planned: 2
--                     Workers Launched: 2
--                     ->  Sort  (cost=35995.04..36001.29 rows=2500 width=49) (actual time=220.379..220.382 rows=50 loops=3)
--                           Sort Key: m.id
--                           Sort Method: quicksort  Memory: 30kB
--                           Worker 0:  Sort Method: quicksort  Memory: 30kB
--                           Worker 1:  Sort Method: quicksort  Memory: 30kB
--                           ->  Partial HashAggregate  (cost=35822.69..35853.94 rows=2500 width=49) (actual time=220.343..220.362 rows=50 loops=3)
--                                 Group Key: m.id
--                                 Batches: 1  Memory Usage: 145kB
--                                 Worker 0:  Batches: 1  Memory Usage: 145kB
--                                 Worker 1:  Batches: 1  Memory Usage: 145kB
--                                 ->  Hash Join  (cost=4602.31..33739.36 rows=416667 width=22) (actual time=33.187..191.395 rows=333333 loops=3)
--                                       Hash Cond: (s.movie_id = m.id)
--                                       ->  Parallel Hash Join  (cost=4517.06..32558.50 rows=416667 width=9) (actual time=32.716..161.885 rows=333333 loops=3)
--                                             Hash Cond: (t.session_id = s.id)
--                                             ->  Parallel Seq Scan on tickets t  (cost=0.00..26947.67 rows=416667 width=9) (actual time=15.901..82.408 rows=333333 loops=3)
--                                                   Filter: (purchase_time >= (now() - '7 days'::interval))
--                                             ->  Parallel Hash  (cost=3046.47..3046.47 rows=117647 width=8) (actual time=16.677..16.677 rows=66667 loops=3)
--                                                   Buckets: 262144  Batches: 1  Memory Usage: 9888kB
--                                                   ->  Parallel Seq Scan on sessions s  (cost=0.00..3046.47 rows=117647 width=8) (actual time=0.028..7.873 rows=66667 loops=3)
--                                       ->  Hash  (cost=54.00..54.00 rows=2500 width=17) (actual time=0.422..0.422 rows=2500 loops=3)
--                                             Buckets: 4096  Batches: 1  Memory Usage: 159kB
--                                             ->  Seq Scan on movies m  (cost=0.00..54.00 rows=2500 width=17) (actual time=0.051..0.251 rows=2500 loops=3)
-- Planning Time: 3.724 ms
-- Execution Time: 227.177 ms
```

Индексы никак не повлияли на скорость стоимость и скорость запроса, постгрес их не использовал.
Как я понял, когда постгрес выбирает слишком много строк он предпочитает Seq Scan вместо Index Scan.
Seq Scan загружает страницы по порядку, что дешевле, чем прыгать по индексу в данном случае.

### принудительное использование индексов для теста:
стоимость повысилась, скорость не изменилась

```sql
-- Limit  (cost=44679.56..44679.57 rows=3 width=49) (actual time=229.909..231.059 rows=3 loops=1)
--   ->  Sort  (cost=44679.56..44685.81 rows=2500 width=49) (actual time=229.907..231.057 rows=3 loops=1)
--         Sort Key: (sum(t.price)) DESC
--         Sort Method: top-N heapsort  Memory: 25kB
--         ->  Finalize GroupAggregate  (cost=43995.13..44647.25 rows=2500 width=49) (actual time=229.814..231.023 rows=50 loops=1)
--               Group Key: m.id
--               ->  Gather Merge  (cost=43995.13..44578.50 rows=5000 width=49) (actual time=229.807..230.968 rows=150 loops=1)
--                     Workers Planned: 2
--                     Workers Launched: 2
--                     ->  Sort  (cost=42995.10..43001.35 rows=2500 width=49) (actual time=219.597..219.599 rows=50 loops=3)
--                           Sort Key: m.id
--                           Sort Method: quicksort  Memory: 30kB
--                           Worker 0:  Sort Method: quicksort  Memory: 30kB
--                           Worker 1:  Sort Method: quicksort  Memory: 30kB
--                           ->  Partial HashAggregate  (cost=42822.76..42854.01 rows=2500 width=49) (actual time=219.545..219.563 rows=50 loops=3)
--                                 Group Key: m.id
--                                 Batches: 1  Memory Usage: 145kB
--                                 Worker 0:  Batches: 1  Memory Usage: 145kB
--                                 Worker 1:  Batches: 1  Memory Usage: 145kB
--                                 ->  Hash Join  (cost=6208.37..40739.42 rows=416667 width=22) (actual time=50.263..189.759 rows=333333 loops=3)
--                                       Hash Cond: (s.movie_id = m.id)
--                                       ->  Parallel Hash Join  (cost=6099.34..39534.78 rows=416667 width=9) (actual time=47.700..156.140 rows=333333 loops=3)
--                                             Hash Cond: (t.session_id = s.id)
--                                             ->  Parallel Index Scan using idx_tickets_purchase_time on tickets t  (cost=0.43..32342.10 rows=416667 width=9) (actual time=0.685..38.576 rows=333333 loops=3)
--                                                   Index Cond: (purchase_time >= (now() - '7 days'::interval))
--                                             ->  Parallel Hash  (cost=4628.32..4628.32 rows=117647 width=8) (actual time=46.798..46.799 rows=66667 loops=3)
--                                                   Buckets: 262144  Batches: 1  Memory Usage: 9920kB
--                                                   ->  Parallel Index Scan using idx_sessions_movie_id on sessions s  (cost=0.29..4628.32 rows=117647 width=8) (actual time=0.902..34.596 rows=66667 loops=3)
--                                       ->  Hash  (cost=77.78..77.78 rows=2500 width=17) (actual time=2.534..2.534 rows=2500 loops=3)
--                                             Buckets: 4096  Batches: 1  Memory Usage: 159kB
--                                             ->  Index Scan using movies_pkey on movies m  (cost=0.28..77.78 rows=2500 width=17) (actual time=0.042..1.830 rows=2500 loops=3)
-- Planning Time: 1.035 ms
-- Execution Time: 231.304 ms
```

## 5 запрос – Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

```sql
EXPLAIN ANALYZE
SELECT h.title,
       s.row_number,
       s.seat_number,
       CASE WHEN t.id IS NULL THEN 'free' ELSE 'taken' END AS seat_status
FROM seats s
JOIN sessions se ON se.hall_id = s.hall_id AND se.id = 15 -- ID конкретного сеанса
JOIN halls h ON se.hall_id = h.id
LEFT JOIN tickets t
    ON t.session_id = se.id
    AND s.row_number = t.row_number
    AND s.seat_number = t.seat_number
ORDER BY s.row_number, s.seat_number;
```

### без использования дополнительных индексов:

```sql
-- Merge Left Join  (cost=9.14..18.10 rows=48 width=49) (actual time=0.228..0.378 rows=48 loops=1)
--   Merge Cond: ((s.row_number = t.row_number) AND (s.seat_number = t.seat_number))
--   ->  Sort  (cost=8.72..8.84 rows=48 width=21) (actual time=0.181..0.185 rows=48 loops=1)
-- "        Sort Key: s.row_number, s.seat_number"
--         Sort Method: quicksort  Memory: 26kB
--         ->  Hash Join  (cost=3.92..7.38 rows=48 width=21) (actual time=0.124..0.161 rows=48 loops=1)
--               Hash Cond: (s.hall_id = se.hall_id)
--               ->  Seq Scan on seats s  (cost=0.00..2.44 rows=144 width=12) (actual time=0.023..0.035 rows=144 loops=1)
--               ->  Hash  (cost=3.90..3.90 rows=1 width=21) (actual time=0.091..0.091 rows=1 loops=1)
--                     Buckets: 1024  Batches: 1  Memory Usage: 9kB
--                     ->  Nested Loop  (cost=0.42..3.90 rows=1 width=21) (actual time=0.078..0.078 rows=1 loops=1)
--                           Join Filter: (h.id = se.hall_id)
--                           ->  Index Scan using sessions_pkey on sessions se  (cost=0.42..2.84 rows=1 width=8) (actual time=0.070..0.070 rows=1 loops=1)
--                                 Index Cond: (id = 15)
--                           ->  Seq Scan on halls h  (cost=0.00..1.03 rows=3 width=13) (actual time=0.003..0.003 rows=1 loops=1)
--   ->  Index Scan using tickets_session_id_row_number_seat_number_key on tickets t  (cost=0.42..8.93 rows=6 width=16) (actual time=0.044..0.172 rows=10 loops=1)
--         Index Cond: (session_id = 15)
-- Planning Time: 4.731 ms
-- Execution Time: 0.474 ms
```

### тест с дополнительным индексом:
изменения производительности не большие

```sql
CREATE INDEX idx_seats_hall_row_seat ON seats (hall_id, row_number, seat_number);
```

```sql
-- Merge Left Join  (cost=8.46..17.41 rows=48 width=49) (actual time=0.223..0.374 rows=48 loops=1)
--   Merge Cond: ((s.row_number = t.row_number) AND (s.seat_number = t.seat_number))
--   ->  Sort  (cost=8.03..8.15 rows=48 width=21) (actual time=0.169..0.173 rows=48 loops=1)
-- "        Sort Key: s.row_number, s.seat_number"
--         Sort Method: quicksort  Memory: 26kB
--         ->  Nested Loop  (cost=0.56..6.69 rows=48 width=21) (actual time=0.115..0.134 rows=48 loops=1)
--               Join Filter: (s.hall_id = se.hall_id)
--               ->  Nested Loop  (cost=0.42..3.90 rows=1 width=21) (actual time=0.073..0.073 rows=1 loops=1)
--                     Join Filter: (h.id = se.hall_id)
--                     ->  Index Scan using sessions_pkey on sessions se  (cost=0.42..2.84 rows=1 width=8) (actual time=0.062..0.062 rows=1 loops=1)
--                           Index Cond: (id = 15)
--                     ->  Seq Scan on halls h  (cost=0.00..1.03 rows=3 width=13) (actual time=0.007..0.007 rows=1 loops=1)
--               ->  Index Only Scan using idx_seats_hall_row_seat on seats s  (cost=0.14..2.19 rows=48 width=12) (actual time=0.040..0.052 rows=48 loops=1)
--                     Index Cond: (hall_id = h.id)
--                     Heap Fetches: 48
--   ->  Index Scan using tickets_session_id_row_number_seat_number_key on tickets t  (cost=0.42..8.93 rows=6 width=16) (actual time=0.050..0.179 rows=10 loops=1)
--         Index Cond: (session_id = 15)
-- Planning Time: 1.218 ms
-- Execution Time: 0.476 ms
```

## 6 запрос – Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс

```sql
EXPLAIN ANALYZE
SELECT MIN(price) AS min_price, MAX(price) AS max_price
FROM tickets
WHERE session_id = 15;
```

### без индексов:
```sql
-- Aggregate  (cost=8.96..8.97 rows=1 width=64) (actual time=0.217..0.218 rows=1 loops=1)
--   ->  Index Scan using tickets_session_id_row_number_seat_number_key on tickets  (cost=0.42..8.93 rows=6 width=5) (actual time=0.061..0.204 rows=10 loops=1)
--         Index Cond: (session_id = 15)
-- Planning Time: 0.805 ms
-- Execution Time: 0.289 ms
```

### с дополнительным индексом:

```sql
CREATE INDEX idx_tickets_session_id ON tickets(session_id);
```

```sql
-- Aggregate  (cost=8.96..8.97 rows=1 width=64) (actual time=0.092..0.092 rows=1 loops=1)
--   ->  Index Scan using idx_tickets_session_id on tickets  (cost=0.42..8.93 rows=6 width=5) (actual time=0.052..0.080 rows=10 loops=1)
--         Index Cond: (session_id = 15)
-- Planning Time: 0.510 ms
-- Execution Time: 0.130 ms
```

## Самые большие объекты в БД (таблицы + индексы)

| object_name                                   | object_type | total_size |
|-----------------------------------------------|-------------|------------|
| tickets                                       | r           | 155 MB     |
| tickets_session_id_row_number_seat_number_key | i           | 39 MB      |
| orders                                        | r           | 36 MB      |
| sessions                                      | r           | 24 MB      |
| tickets_pkey                                  | i           | 21 MB      |
| idx_tickets_session_id                        | i           | 15 MB      |
| users                                         | r           | 15 MB      |
| orders_pkey                                   | i           | 11 MB      |
| users_email_key                               | i           | 6920 kB    |
| idx_tickets_purchase_time                     | i           | 6312 kB    |
| idx_sessions_date                             | i           | 5160 kB    |
| sessions_pkey                                 | i           | 4408 kB    |
| users_pkey                                    | i           | 2208 kB    |
| pg_proc                                       | r           | 1232 kB    |
| pg_attribute                                  | r           | 744 kB     |
