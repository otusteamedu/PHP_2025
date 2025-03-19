-- Простые запросы

-- Фильмы которые идут сегодня
SELECT DISTINCT m.id, m.title, m.description, m.release_date, m.duration_minutes
FROM movies m
         JOIN sessions s ON m.id = s.movie_id
WHERE DATE(s.start_time) = CURRENT_DATE;

-- Продано билетов за неделю
SELECT COUNT(*) AS total_tickets
FROM tickets
WHERE purchase_time >= NOW() - INTERVAL '7 days';

-- Афиша, фильмы которые показывают сегодня
SELECT m.title, s.start_time, s.hall_id
FROM movies m
         JOIN sessions s ON m.id = s.movie_id
WHERE DATE(s.start_time) = CURRENT_DATE
ORDER BY s.start_time;


-- Сложные запросы:

-- Топ 3 самых прибыльных фильмов за неделю
SELECT m.title, SUM(t.price) AS total_revenue
FROM tickets t
         JOIN sessions s ON t.session_id = s.id
         JOIN movies m ON s.movie_id = m.id
WHERE t.purchase_time >= NOW() - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
    LIMIT 3;

-- Схема зала с занятыми местами
SELECT s.hall_id,
       s.row_number,
       s.seat_number,
       CASE WHEN t.id IS NULL THEN 'free' ELSE 'taken' END AS seat_status
FROM seats s
         LEFT JOIN tickets t
                   ON s.hall_id = t.hall_id AND s.row_number = t.row_number AND s.seat_number = t.seat_number
                       AND t.session_id = 1 -- ID конкретного сеанса
WHERE s.hall_id = 1 -- ID нужного зала
ORDER BY s.row_number, s.seat_number;

-- Диапазон цен за билет на конкретный сеанс
SELECT MIN(price) AS min_price, MAX(price) AS max_price
FROM tickets
WHERE session_id = 1;


-- Наполнение БД до 10000 строк

INSERT INTO users (email)
SELECT 'user' || generate_series(1, 1000) || '@example.com';

INSERT INTO sessions (hall_id, movie_id, start_time, end_time, price_regular, price_vip)
SELECT
    (floor(random() * 3) + 1)::INT AS hall_id,  -- Генерация только 1, 2 или 3
    (floor(random() * 5) + 1)::INT AS movie_id,  -- 5 фильмов
    NOW() + (random() * INTERVAL '30 days') AS start_time,
    NOW() + (random() * INTERVAL '30 days') + INTERVAL '2 hours' AS end_time,
    (random() * 200 + 500)::INT AS price_regular,
    (random() * 300 + 800)::INT AS price_vip
FROM generate_series(1, 2000);

INSERT INTO orders (user_id, order_time, total_amount)
SELECT
    floor(random() * 1000) + 1,
    NOW() - (random() * interval '30 days'),
    (random() * 300 + 800)::INT  -- случайная сумма от 800 до 1100
FROM generate_series(1, 1000);  -- создаем 1000 заказов

INSERT INTO tickets (order_id, session_id, hall_id, row_number, seat_number, seat_type, price)
SELECT
    o.id AS order_id,
    s.id AS session_id,
    se.hall_id,
    se.row_number,
    se.seat_number,
    CASE WHEN random() > 0.8 THEN 'vip' ELSE 'regular' END AS seat_type,
    (random() * 300 + 800)::INT AS price
FROM sessions s
         JOIN seats se ON s.hall_id = se.hall_id
         JOIN orders o ON o.id = (SELECT id FROM orders ORDER BY random() LIMIT 1)  -- случайный заказ
ORDER BY random()
LIMIT 5000
ON CONFLICT (session_id, row_number, seat_number) DO NOTHING;

-- Aggregate  (cost=147.00..147.01 rows=1 width=8) (actual time=2.385..2.385 rows=1 loops=1)
--   ->  Seq Scan on tickets  (cost=0.00..134.50 rows=5000 width=0) (actual time=0.010..2.083 rows=5000 loops=1)
--         Filter: (purchase_time >= (now() - '7 days'::interval))
-- Planning Time: 0.352 ms
-- Execution Time: 2.404 ms
EXPLAIN ANALYZE SELECT COUNT(*) FROM tickets WHERE purchase_time >= NOW() - INTERVAL '7 days';

-- После добавления индекса на tickets.purchase_time
-- Aggregate  (cost=128.29..128.30 rows=1 width=8) (actual time=1.190..1.190 rows=1 loops=1)
--   ->  Index Only Scan using idx_tickets_purchase_time on tickets  (cost=0.29..115.79 rows=5000 width=0) (actual time=0.108..0.829 rows=5000 loops=1)
--         Index Cond: (purchase_time >= (now() - '7 days'::interval))
--         Heap Fetches: 0
-- Planning Time: 0.447 ms
-- Execution Time: 1.225 ms
EXPLAIN ANALYZE SELECT COUNT(*) FROM tickets WHERE purchase_time >= NOW() - INTERVAL '7 days';

-- На небольших данныъ постгрес не использовал индексы
-- Seq Scan on movies  (cost=0.00..1.07 rows=1 width=560) (actual time=0.013..0.014 rows=0 loops=1)
--   Filter: (release_date = CURRENT_DATE)
--   Rows Removed by Filter: 5
-- Planning Time: 0.086 ms
-- Execution Time: 0.025 ms
EXPLAIN ANALYZE SELECT * FROM movies WHERE release_date = CURRENT_DATE;
-- Включил принудительно для проверки
-- Index Scan using idx_movies_release_date on movies  (cost=0.14..8.15 rows=1 width=560) (actual time=0.020..0.021 rows=0 loops=1)
--   Index Cond: (release_date = CURRENT_DATE)
-- Planning Time: 0.101 ms
-- Execution Time: 0.036 ms
-- SET enable_seqscan = OFF;
-- EXPLAIN ANALYZE SELECT * FROM movies WHERE release_date = CURRENT_DATE;


-- 3 самых прибыльных фильмов за неделю

-- Limit  (cost=262.13..262.14 rows=3 width=548) (actual time=7.469..7.473 rows=3 loops=1)
--   ->  Sort  (cost=262.13..262.14 rows=5 width=548) (actual time=7.468..7.471 rows=3 loops=1)
--         Sort Key: (sum(t.price)) DESC
--         Sort Method: quicksort  Memory: 25kB
--         ->  HashAggregate  (cost=262.01..262.07 rows=5 width=548) (actual time=7.456..7.460 rows=5 loops=1)
--               Group Key: m.title
--               Batches: 1  Memory Usage: 24kB
--               ->  Hash Join  (cost=65.11..237.01 rows=5000 width=521) (actual time=0.748..5.851 rows=5000 loops=1)
--                     Hash Cond: (s.movie_id = m.id)
--                     ->  Hash Join  (cost=64.00..211.65 rows=5000 width=9) (actual time=0.720..4.691 rows=5000 loops=1)
--                           Hash Cond: (t.session_id = s.id)
--                           ->  Seq Scan on tickets t  (cost=0.00..134.50 rows=5000 width=9) (actual time=0.008..2.394 rows=5000 loops=1)
--                                 Filter: (purchase_time >= (now() - '7 days'::interval))
--                           ->  Hash  (cost=39.00..39.00 rows=2000 width=8) (actual time=0.706..0.707 rows=2000 loops=1)
--                                 Buckets: 2048  Batches: 1  Memory Usage: 95kB
--                                 ->  Seq Scan on sessions s  (cost=0.00..39.00 rows=2000 width=8) (actual time=0.004..0.293 rows=2000 loops=1)
--                     ->  Hash  (cost=1.05..1.05 rows=5 width=520) (actual time=0.021..0.022 rows=5 loops=1)
--                           Buckets: 1024  Batches: 1  Memory Usage: 9kB
--                           ->  Seq Scan on movies m  (cost=0.00..1.05 rows=5 width=520) (actual time=0.016..0.017 rows=5 loops=1)
-- Planning Time: 0.577 ms
-- Execution Time: 7.524 ms
EXPLAIN ANALYZE SELECT m.title, SUM(t.price) as revenue
FROM tickets t
         JOIN sessions s ON t.session_id = s.id
         JOIN movies m ON s.movie_id = m.id
WHERE t.purchase_time >= NOW() - INTERVAL '7 days'
GROUP BY m.title
ORDER BY revenue DESC
LIMIT 3;

-- Limit  (cost=262.13..262.14 rows=3 width=548) (actual time=6.795..6.800 rows=3 loops=1)
--   ->  Sort  (cost=262.13..262.14 rows=5 width=548) (actual time=6.794..6.797 rows=3 loops=1)
--         Sort Key: (sum(t.price)) DESC
--         Sort Method: quicksort  Memory: 25kB
--         ->  HashAggregate  (cost=262.01..262.07 rows=5 width=548) (actual time=6.782..6.787 rows=5 loops=1)
--               Group Key: m.title
--               Batches: 1  Memory Usage: 24kB
--               ->  Hash Join  (cost=65.11..237.01 rows=5000 width=521) (actual time=0.575..5.242 rows=5000 loops=1)
--                     Hash Cond: (s.movie_id = m.id)
--                     ->  Hash Join  (cost=64.00..211.65 rows=5000 width=9) (actual time=0.557..4.121 rows=5000 loops=1)
--                           Hash Cond: (t.session_id = s.id)
--                           ->  Seq Scan on tickets t  (cost=0.00..134.50 rows=5000 width=9) (actual time=0.005..2.244 rows=5000 loops=1)
--                                 Filter: (purchase_time >= (now() - '7 days'::interval))
--                           ->  Hash  (cost=39.00..39.00 rows=2000 width=8) (actual time=0.547..0.548 rows=2000 loops=1)
--                                 Buckets: 2048  Batches: 1  Memory Usage: 95kB
--                                 ->  Seq Scan on sessions s  (cost=0.00..39.00 rows=2000 width=8) (actual time=0.003..0.265 rows=2000 loops=1)
--                     ->  Hash  (cost=1.05..1.05 rows=5 width=520) (actual time=0.013..0.014 rows=5 loops=1)
--                           Buckets: 1024  Batches: 1  Memory Usage: 9kB
--                           ->  Seq Scan on movies m  (cost=0.00..1.05 rows=5 width=520) (actual time=0.008..0.010 rows=5 loops=1)
-- Planning Time: 0.912 ms
-- Execution Time: 6.849 ms
EXPLAIN ANALYZE SELECT m.title, SUM(t.price) as revenue
FROM tickets t
         JOIN sessions s ON t.session_id = s.id
         JOIN movies m ON s.movie_id = m.id
WHERE t.purchase_time >= NOW() - INTERVAL '7 days'
GROUP BY m.title
ORDER BY revenue DESC
LIMIT 3;

-- Итого на маленьких данных постгресс индексы не использовал, только если принудительно

-- Заполняем данными до 1000000

INSERT INTO users (email)
SELECT 'user' || generate_series(1, 100000) || '@example.com';

INSERT INTO movies (title, description, release_date, duration_minutes)
SELECT 'Фильм ' || generate_series(1, 50),
       'Описание фильма ' || generate_series(1, 50),
       NOW() - (random() * INTERVAL '365 days'),
       floor(random() * 120 + 80)
FROM generate_series(1, 50);

INSERT INTO sessions (hall_id, movie_id, start_time, end_time, price_regular, price_vip)
SELECT
    (floor(random() * 3) + 1)::INT,
    (floor(random() * 50) + 1)::INT,
    NOW() + (random() * INTERVAL '30 days'),
    NOW() + (random() * INTERVAL '30 days') + INTERVAL '2 hours',
    floor(random() * 500 + 300),
    floor(random() * 800 + 500)
FROM generate_series(1, 200000);

INSERT INTO orders (user_id, order_time, total_amount)
SELECT
    floor(random() * 1000) + 1,
    NOW() - (random() * interval '30 days'),
    (random() * 300 + 800)::INT  -- случайная сумма от 800 до 1100
FROM generate_series(1, 500000);  -- создаем 500000 заказов

INSERT INTO tickets (order_id, session_id, hall_id, row_number, seat_number, seat_type, price)
SELECT
    o.id AS order_id,
    s.id AS session_id,
    se.hall_id,
    se.row_number,
    se.seat_number,
    CASE WHEN random() > 0.8 THEN 'vip' ELSE 'regular' END AS seat_type,
    (random() * 300 + 800)::INT AS price
FROM sessions s
         JOIN seats se ON s.hall_id = se.hall_id
         JOIN orders o ON o.id = (SELECT id FROM orders ORDER BY random() LIMIT 1)
ORDER BY random()
LIMIT 1000000
ON CONFLICT (session_id, row_number, seat_number) DO NOTHING;



EXPLAIN ANALYZE
SELECT m.id, m.title, SUM(t.price) AS total_revenue
FROM tickets t
         JOIN sessions s ON t.session_id = s.id
         JOIN movies m ON s.movie_id = m.id
WHERE t.purchase_time >= NOW() - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
LIMIT 3;


-- Hash Left Join  (cost=36.57..39.64 rows=48 width=46) (actual time=0.345..0.367 rows=48 loops=1)
--   Hash Cond: ((se.row_number = t.row_number) AND (se.seat_number = t.seat_number))
--   InitPlan 1
--     ->  Index Scan using sessions_pkey on sessions  (cost=0.42..8.44 rows=1 width=4) (actual time=0.011..0.012 rows=1 loops=1)
--           Index Cond: (id = 12345)
--   ->  Seq Scan on seats se  (cost=0.00..2.80 rows=48 width=18) (actual time=0.283..0.290 rows=48 loops=1)
--         Filter: (hall_id = (InitPlan 1).col1)
--         Rows Removed by Filter: 96
--   ->  Hash  (cost=28.10..28.10 rows=2 width=16) (actual time=0.056..0.056 rows=6 loops=1)
--         Buckets: 1024  Batches: 1  Memory Usage: 9kB
--         ->  Bitmap Heap Scan on tickets t  (cost=4.47..28.10 rows=2 width=16) (actual time=0.045..0.052 rows=6 loops=1)
--               Recheck Cond: (session_id = 12345)
--               Filter: (hall_id = (InitPlan 1).col1)
--               Heap Blocks: exact=6
--               ->  Bitmap Index Scan on idx_tickets_price  (cost=0.00..4.47 rows=6 width=0) (actual time=0.023..0.023 rows=6 loops=1)
--                     Index Cond: (session_id = 12345)
-- Planning Time: 0.366 ms
-- Execution Time: 0.398 ms
EXPLAIN ANALYZE
SELECT
    se.row_number,
    se.seat_number,
    se.seat_type,
    CASE
        WHEN t.id IS NULL THEN 'free'
        ELSE 'occupied'
        END AS seat_status
FROM seats se
         LEFT JOIN tickets t
                   ON se.hall_id = t.hall_id
                       AND se.row_number = t.row_number
                       AND se.seat_number = t.seat_number
                       AND t.session_id = 12345
WHERE se.hall_id = (SELECT hall_id FROM sessions WHERE id = 12345);

-- Limit  (cost=619415.19..619415.22 rows=10 width=52) (actual time=2545.443..2551.919 rows=10 loops=1)
--   ->  Sort  (cost=619415.19..619915.19 rows=200000 width=52) (actual time=2545.442..2551.917 rows=10 loops=1)
-- "        Sort Key: (round((((count(tickets.session_id))::numeric * 100.0) / (count(*))::numeric), 2)) DESC"
--         Sort Method: top-N heapsort  Memory: 26kB
--         ->  Finalize HashAggregate  (cost=608249.51..615093.26 rows=200000 width=52) (actual time=2451.128..2533.956 rows=200000 loops=1)
--               Group Key: s.id
--               Planned Partitions: 4  Batches: 5  Memory Usage: 8241kB  Disk Usage: 12016kB
--               ->  Gather  (cost=527256.87..593374.51 rows=200000 width=20) (actual time=2083.431..2404.394 rows=400000 loops=1)
--                     Workers Planned: 1
--                     Workers Launched: 1
--                     ->  Partial HashAggregate  (cost=526256.87..572374.51 rows=200000 width=20) (actual time=2080.669..2380.468 rows=200000 loops=2)
--                           Group Key: s.id
--                           Planned Partitions: 4  Batches: 5  Memory Usage: 8241kB  Disk Usage: 81024kB
--                           Worker 0:  Batches: 5  Memory Usage: 8241kB  Disk Usage: 80768kB
--                           ->  Parallel Hash Left Join  (cost=25968.58..194492.15 rows=5647059 width=8) (actual time=900.695..1590.566 rows=4800000 loops=2)
--                                 Hash Cond: ((se.row_number = tickets.row_number) AND (se.seat_number = tickets.seat_number) AND (s.id = tickets.session_id))
--                                 ->  Hash Join  (cost=4.24..66874.21 rows=5647059 width=12) (actual time=0.109..267.216 rows=4800000 loops=2)
--                                       Hash Cond: (s.hall_id = se.hall_id)
--                                       ->  Parallel Seq Scan on sessions s  (cost=0.00..3046.47 rows=117647 width=8) (actual time=0.047..7.046 rows=100000 loops=2)
--                                       ->  Hash  (cost=2.44..2.44 rows=144 width=12) (actual time=0.045..0.045 rows=144 loops=2)
--                                             Buckets: 1024  Batches: 1  Memory Usage: 15kB
--                                             ->  Seq Scan on seats se  (cost=0.00..2.44 rows=144 width=12) (actual time=0.025..0.031 rows=144 loops=2)
--                                 ->  Parallel Hash  (cost=16637.67..16637.67 rows=416667 width=12) (actual time=151.017..151.017 rows=500000 loops=2)
--                                       Buckets: 262144  Batches: 8  Memory Usage: 7968kB
--                                       ->  Parallel Seq Scan on tickets  (cost=0.00..16637.67 rows=416667 width=12) (actual time=0.016..93.841 rows=500000 loops=2)
--                                             Filter: (purchase_time >= (now() - '30 days'::interval))
-- Planning Time: 1.791 ms
-- Execution Time: 2556.837 ms
EXPLAIN ANALYZE
WITH filtered_tickets AS (
    SELECT session_id, row_number, seat_number
    FROM tickets
    WHERE purchase_time >= NOW() - INTERVAL '30 days'
)
SELECT
    s.id AS session_id,
    COUNT(t.session_id) AS sold_tickets,
    COUNT(*) AS total_seats,
    ROUND((COUNT(t.session_id) * 100.0) / COUNT(*), 2) AS occupancy_rate
FROM sessions s
         JOIN seats se ON s.hall_id = se.hall_id
         LEFT JOIN filtered_tickets t
                   ON t.session_id = s.id
                       AND se.row_number = t.row_number
                       AND se.seat_number = t.seat_number
GROUP BY s.id
ORDER BY occupancy_rate DESC
LIMIT 10;

-- Limit  (cost=630908.91..630908.93 rows=10 width=36) (actual time=2581.465..2589.408 rows=10 loops=1)
--   ->  Sort  (cost=630908.91..631408.91 rows=200000 width=36) (actual time=2581.464..2589.406 rows=10 loops=1)
-- "        Sort Key: (round((((count(t.session_id))::numeric * 100.0) / (NULLIF(count(*), 0))::numeric), 2)) DESC"
--         Sort Method: top-N heapsort  Memory: 25kB
--         ->  Finalize HashAggregate  (cost=619243.23..626586.98 rows=200000 width=36) (actual time=2491.514..2574.913 rows=200000 loops=1)
--               Group Key: s.id
--               Planned Partitions: 4  Batches: 5  Memory Usage: 8241kB  Disk Usage: 12032kB
--               ->  Gather  (cost=538250.58..604368.23 rows=200000 width=20) (actual time=2131.573..2446.690 rows=400000 loops=1)
--                     Workers Planned: 1
--                     Workers Launched: 1
--                     ->  Partial HashAggregate  (cost=537250.58..583368.23 rows=200000 width=20) (actual time=2127.889..2422.837 rows=200000 loops=2)
--                           Group Key: s.id
--                           Planned Partitions: 4  Batches: 5  Memory Usage: 8241kB  Disk Usage: 80752kB
--                           Worker 0:  Batches: 5  Memory Usage: 8241kB  Disk Usage: 81032kB
--                           ->  Parallel Hash Left Join  (cost=33755.29..205485.87 rows=5647059 width=8) (actual time=914.018..1629.251 rows=4800000 loops=2)
--                                 Hash Cond: ((s.id = t.session_id) AND (se.row_number = t.row_number) AND (se.seat_number = t.seat_number))
--                                 ->  Hash Left Join  (cost=16.52..70093.49 rows=5647059 width=12) (actual time=0.068..263.536 rows=4800000 loops=2)
--                                       Hash Cond: (s.hall_id = se.hall_id)
--                                       ->  Parallel Index Scan using sessions_pkey on sessions s  (cost=0.42..6253.89 rows=117647 width=8) (actual time=0.008..8.272 rows=100000 loops=2)
--                                       ->  Hash  (cost=14.30..14.30 rows=144 width=12) (actual time=0.049..0.049 rows=144 loops=2)
--                                             Buckets: 1024  Batches: 1  Memory Usage: 15kB
--                                             ->  Index Only Scan using idx_seats_hall_row_seat on seats se  (cost=0.14..14.30 rows=144 width=12) (actual time=0.020..0.029 rows=144 loops=2)
--                                                   Heap Fetches: 288
--                                 ->  Parallel Hash  (cost=24412.10..24412.10 rows=416667 width=12) (actual time=128.232..128.232 rows=500000 loops=2)
--                                       Buckets: 262144  Batches: 8  Memory Usage: 7936kB
--                                       ->  Parallel Index Scan using idx_tickets_purchase_time on tickets t  (cost=0.43..24412.10 rows=416667 width=12) (actual time=0.035..59.204 rows=500000 loops=2)
--                                             Index Cond: (purchase_time >= (now() - '30 days'::interval))
-- Planning Time: 0.548 ms
-- Execution Time: 2594.566 ms
EXPLAIN ANALYZE
SELECT s.id,
       ROUND((COUNT(t.session_id) * 100.0) / NULLIF(COUNT(*), 0), 2) AS occupancy_rate
FROM sessions s
         LEFT JOIN seats se ON s.hall_id = se.hall_id
         LEFT JOIN tickets t
                   ON t.session_id = s.id
                       AND t.row_number = se.row_number
                       AND t.seat_number = se.seat_number
                       AND t.purchase_time >= NOW() - INTERVAL '30 days'
GROUP BY s.id
ORDER BY occupancy_rate DESC
LIMIT 10;


-- После обновления конфига
-- shared_buffers = '4GB'
-- work_mem = '256MB'
-- temp_buffers = '64MB'
-- effective_cache_size = '8GB'
-- random_page_cost = 1.2

-- Limit  (cost=208156.01..208156.04 rows=10 width=36) (actual time=1918.444..1919.088 rows=10 loops=1)
--   ->  Sort  (cost=208156.01..208656.01 rows=200000 width=36) (actual time=1918.442..1919.086 rows=10 loops=1)
-- "        Sort Key: (round((((count(t.session_id))::numeric * 100.0) / (NULLIF(count(*), 0))::numeric), 2)) DESC"
--         Sort Method: top-N heapsort  Memory: 25kB
--         ->  Finalize HashAggregate  (cost=198834.08..203834.08 rows=200000 width=36) (actual time=1854.314..1904.688 rows=200000 loops=1)
--               Group Key: s.id
--               Batches: 1  Memory Usage: 28689kB
--               ->  Gather  (cost=175334.08..197334.08 rows=200000 width=20) (actual time=1815.005..1829.209 rows=200000 loops=1)
--                     Workers Planned: 1
--                     Workers Launched: 1
--                     ->  Partial HashAggregate  (cost=174334.08..176334.08 rows=200000 width=20) (actual time=1801.079..1807.959 rows=100000 loops=2)
--                           Group Key: s.id
--                           Batches: 1  Memory Usage: 14353kB
--                           Worker 0:  Batches: 1  Memory Usage: 14353kB
--                           ->  Parallel Hash Left Join  (cost=20640.57..131981.14 rows=5647059 width=8) (actual time=157.870..1487.916 rows=4800000 loops=2)
--                                 Hash Cond: ((s.id = t.session_id) AND (se.row_number = t.row_number) AND (se.seat_number = t.seat_number))
--                                 ->  Hash Left Join  (cost=4.24..66874.21 rows=5647059 width=12) (actual time=0.903..257.267 rows=4800000 loops=2)
--                                       Hash Cond: (s.hall_id = se.hall_id)
--                                       ->  Parallel Seq Scan on sessions s  (cost=0.00..3046.47 rows=117647 width=8) (actual time=0.040..6.868 rows=100000 loops=2)
--                                       ->  Hash  (cost=2.44..2.44 rows=144 width=12) (actual time=0.838..0.838 rows=144 loops=2)
--                                             Buckets: 1024  Batches: 1  Memory Usage: 15kB
--                                             ->  Seq Scan on seats se  (cost=0.00..2.44 rows=144 width=12) (actual time=0.815..0.821 rows=144 loops=2)
--                                 ->  Parallel Hash  (cost=14991.16..14991.16 rows=322581 width=12) (actual time=156.386..156.386 rows=500000 loops=2)
--                                       Buckets: 1048576  Batches: 1  Memory Usage: 55136kB
--                                       ->  Parallel Seq Scan on tickets t  (cost=0.00..14991.16 rows=322581 width=12) (actual time=0.049..90.641 rows=500000 loops=2)
--                                             Filter: (purchase_time >= (now() - '30 days'::interval))
-- Planning Time: 6.503 ms
-- Execution Time: 1920.833 ms
EXPLAIN ANALYZE
SELECT s.id,
       ROUND((COUNT(t.session_id) * 100.0) / NULLIF(COUNT(*), 0), 2) AS occupancy_rate
FROM sessions s
         LEFT JOIN seats se ON s.hall_id = se.hall_id
         LEFT JOIN tickets t
                   ON t.session_id = s.id
                       AND t.row_number = se.row_number
                       AND t.seat_number = se.seat_number
                       AND t.purchase_time >= NOW() - INTERVAL '30 days'
GROUP BY s.id
ORDER BY occupancy_rate DESC
LIMIT 10;


ALTER TABLE seats DROP CONSTRAINT seats_pkey;
ALTER TABLE seats ADD COLUMN id SERIAL PRIMARY KEY;

ALTER TABLE tickets ADD COLUMN seat_id INT;
UPDATE tickets t
SET seat_id = s.id
FROM seats s
WHERE t.hall_id = s.hall_id
  AND t.row_number = s.row_number
  AND t.seat_number = s.seat_number;

ALTER TABLE tickets
    ADD CONSTRAINT tickets_seat_id_fkey FOREIGN KEY (seat_id) REFERENCES seats(id);

EXPLAIN ANALYZE
SELECT s.id,
       ROUND((COUNT(t.session_id) * 100.0) / NULLIF(COUNT(se.id), 0), 2) AS occupancy_rate
FROM sessions s
         LEFT JOIN seats se ON s.hall_id = se.hall_id
         LEFT JOIN tickets t
                   ON t.session_id = s.id
                       AND t.seat_id = se.id  -- ✅ Используем seat_id вместо row_number и seat_number
                       AND t.purchase_time >= NOW() - INTERVAL '30 days'
GROUP BY s.id
ORDER BY occupancy_rate DESC
LIMIT 10;



SET enable_seqscan = OFF;
SET enable_seqscan = ON;
