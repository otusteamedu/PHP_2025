# Простые запросы в которых задействована одна таблица

## 1. Сумма проданных билетов за сеанс

### Запрос
```sql
EXPLAIN ANALYZE SELECT 
    COALESCE(SUM(final_price), 0) AS total_revenue
FROM 
    tickets
WHERE 
    session_id = 86;
```

### План на БД до 10000 строк
<pre>
|QUERY PLAN                                                                                            |
+------------------------------------------------------------------------------------------------------+
|Aggregate  (cost=60.50..60.51 rows=1 width=4) (actual time=0.192..0.192 rows=1 loops=1)               |
|  ->  Seq Scan on tickets  (cost=0.00..60.50 rows=1 width=4) (actual time=0.056..0.189 rows=1 loops=1)|
|        Filter: (session_id = 86)                                                                     |
|        Rows Removed by Filter: 2999                                                                  |
|Planning Time: 0.082 ms                                                                               |
|Execution Time: 0.238 ms                                                                              |
</pre>

### План на БД до 10000000 строк
<pre>
|QUERY PLAN                                                                                                                       |
+---------------------------------------------------------------------------------------------------------------------------------+
|Finalize Aggregate  (cost=13599.23..13599.24 rows=1 width=4) (actual time=29.575..33.047 rows=1 loops=1)                         |
|  ->  Gather  (cost=13599.01..13599.22 rows=2 width=4) (actual time=29.471..33.041 rows=3 loops=1)                               |
|        Workers Planned: 2                                                                                                       |
|        Workers Launched: 2                                                                                                      |
|        ->  Partial Aggregate  (cost=12599.01..12599.02 rows=1 width=4) (actual time=26.044..26.045 rows=1 loops=3)              |
|              ->  Parallel Seq Scan on tickets  (cost=0.00..12598.96 rows=21 width=4) (actual time=1.066..26.015 rows=23 loops=3)|
|                    Filter: (session_id = 86)                                                                                    |
|                    Rows Removed by Filter: 334310                                                                               |
|Planning Time: 0.442 ms                                                                                                          |
|Execution Time: 33.112 ms                                                                                                        |
</pre>

### План на БД до 10000000 строк, что удалось улучшить
<pre>
QUERY PLAN                                                                                                                           |
-------------------------------------------------------------------------------------------------------------------------------------+
Aggregate  (cost=196.86..196.87 rows=1 width=4) (actual time=0.306..0.307 rows=1 loops=1)                                            |
  ->  Bitmap Heap Scan on tickets  (cost=4.82..196.73 rows=51 width=4) (actual time=0.155..0.279 rows=70 loops=1)                    |
        Recheck Cond: (session_id = 86)                                                                                              |
        Heap Blocks: exact=70                                                                                                        |
        ->  Bitmap Index Scan on idx_tickets_session_id  (cost=0.00..4.81 rows=51 width=0) (actual time=0.105..0.106 rows=70 loops=1)|
              Index Cond: (session_id = 86)                                                                                          |
Planning Time: 0.395 ms                                                                                                              |
Execution Time: 0.386 ms                                                                                                             |
</pre>

### Перечень оптимизаций с пояснениями
Добавлен индекс для поиска билетов по session_id (tickets.session_id) так как в запросе используется фильтрация и суммирование - без индекса будет скан всей таблицы (Seq Scan)
```sql
CREATE INDEX idx_tickets_session_id ON public.tickets(session_id);
```





## 2. Количество сеансов за последнюю неделю
```sql
EXPLAIN ANALYZE SELECT 
    COUNT(*) AS sessions_count
FROM 
    sessions
WHERE 
    session_start >= NOW() - INTERVAL '7 days';
```

### План на БД до 10000 строк
<pre>
|QUERY PLAN                                                                                                     |
+---------------------------------------------------------------------------------------------------------------+
|Aggregate  (cost=294.00..294.01 rows=1 width=8) (actual time=2.528..2.529 rows=1 loops=1)                      |
|  ->  Seq Scan on sessions  (cost=0.00..269.00 rows=9999 width=0) (actual time=0.012..2.131 rows=10000 loops=1)|
|        Filter: (session_start >= (now() - '7 days'::interval))                                                |
|Planning Time: 0.078 ms                                                                                        |
|Execution Time: 2.545 ms                                                                                       |
</pre>

### План на БД до 10000000 строк
<pre>
QUERY PLAN                                                                                                       |
-----------------------------------------------------------------------------------------------------------------+
Aggregate  (cost=587.00..587.00 rows=1 width=8) (actual time=11.227..11.241 rows=1 loops=1)                      |
  ->  Seq Scan on sessions  (cost=0.00..537.00 rows=19998 width=0) (actual time=0.060..10.443 rows=20000 loops=1)|
        Filter: (session_start >= (now() - '7 days'::interval))                                                  |
Planning Time: 3.014 ms                                                                                          |
Execution Time: 11.534 ms                                                                                        |
</pre>

### План на БД до 10000000 строк, что удалось улучшить
<pre>
QUERY PLAN                                                                                                      |
----------------------------------------------------------------------------------------------------------------+
Aggregate  (cost=587.00..587.01 rows=1 width=8) (actual time=4.228..4.229 rows=1 loops=1)                       |
  ->  Seq Scan on sessions  (cost=0.00..537.00 rows=20000 width=0) (actual time=0.007..3.608 rows=20000 loops=1)|
        Filter: (session_start >= (now() - '7 days'::interval))                                                 |
Planning Time: 0.496 ms                                                                                         |
Execution Time: 4.255 ms                                                                                        |
</pre>

### Перечень оптимизаций с пояснениями
Добавлен индекс для поиска времени начала фильма sessions.session_start так как этот столбец интенсивно используется при фильтрации по дате
```sql
CREATE INDEX idx_sessions_session_start ON public.sessions(session_start);
```






## 3 Фильмы которые показывают сегодня
```sql
EXPLAIN ANALYZE SELECT DISTINCT 
    film_id
FROM 
    sessions
WHERE 
    session_start::date = CURRENT_DATE;
```

<pre>
|QUERY PLAN                                                                                                  |
+------------------------------------------------------------------------------------------------------------+
|Unique  (cost=1.19..1.19 rows=1 width=8) (actual time=0.322..0.333 rows=3 loops=1)                          |
|  ->  Sort  (cost=1.19..1.19 rows=1 width=8) (actual time=0.313..0.322 rows=4 loops=1)                      |
|        Sort Key: film_id                                                                                   |
|        Sort Method: quicksort  Memory: 25kB                                                                |
|        ->  Seq Scan on sessions  (cost=0.00..1.18 rows=1 width=8) (actual time=0.162..0.171 rows=4 loops=1)|
|              Filter: ((session_start)::date = CURRENT_DATE)                                                |
|              Rows Removed by Filter: 7                                                                     |
|Planning Time: 1.425 ms                                                                                     |
|Execution Time: 0.621 ms                                                                                    |
</pre>

### План на БД до 10000 строк
<pre>
|QUERY PLAN                                                                                                       |
+-----------------------------------------------------------------------------------------------------------------+
|Unique  (cost=270.41..270.66 rows=32 width=8) (actual time=0.982..1.018 rows=50 loops=1)                         |
|  ->  Sort  (cost=270.41..270.54 rows=50 width=8) (actual time=0.981..0.994 rows=338 loops=1)                    |
|        Sort Key: film_id                                                                                        |
|        Sort Method: quicksort  Memory: 25kB                                                                     |
|        ->  Seq Scan on sessions  (cost=0.00..269.00 rows=50 width=8) (actual time=0.014..0.955 rows=338 loops=1)|
|              Filter: ((session_start)::date = CURRENT_DATE)                                                     |
|              Rows Removed by Filter: 9662                                                                       |
|Planning Time: 0.059 ms                                                                                          |
|Execution Time: 1.033 ms                                                                                         |
</pre>

### План на БД до 10000000 строк
<pre>
QUERY PLAN                                                                                                        |
------------------------------------------------------------------------------------------------------------------+
Unique  (cost=540.32..540.82 rows=43 width=8) (actual time=3.972..4.024 rows=50 loops=1)                          |
  ->  Sort  (cost=540.32..540.57 rows=100 width=8) (actual time=3.962..3.987 rows=441 loops=1)                    |
        Sort Key: film_id                                                                                         |
        Sort Method: quicksort  Memory: 25kB                                                                      |
        ->  Seq Scan on sessions  (cost=0.00..537.00 rows=100 width=8) (actual time=0.030..3.716 rows=441 loops=1)|
              Filter: ((session_start)::date = CURRENT_DATE)                                                      |
              Rows Removed by Filter: 19559                                                                       |
Planning Time: 2.539 ms                                                                                           |
Execution Time: 4.206 ms                                                                                          |
</pre>

### План на БД до 10000000 строк, что удалось улучшить
<pre>
QUERY PLAN                                                                                                         |
-------------------------------------------------------------------------------------------------------------------+
Unique  (cost=540.32..540.82 rows=43 width=8) (actual time=2.163..2.327 rows=50 loops=1)                           |
  ->  Sort  (cost=540.32..540.57 rows=100 width=8) (actual time=2.161..2.240 rows=1739 loops=1)                    |
        Sort Key: film_id                                                                                          |
        Sort Method: quicksort  Memory: 49kB                                                                       |
        ->  Seq Scan on sessions  (cost=0.00..537.00 rows=100 width=8) (actual time=0.023..1.988 rows=1739 loops=1)|
              Filter: ((session_start)::date = CURRENT_DATE)                                                       |
              Rows Removed by Filter: 18261                                                                        |
Planning Time: 0.472 ms                                                                                            |
Execution Time: 2.401 ms                                                                                           |
</pre>

### Перечень оптимизаций с пояснениями
Тот же индекс для session_start помогает и в этом запросе тоже 





## 4. Подсчёт дохода от проданных билетов за неделю
```sql
EXPLAIN ANALYZE SELECT
    SUM(t.final_price) AS total_revenue_week
FROM
    tickets t
JOIN
    sessions s ON t.session_id = s.id
WHERE
    s.session_start >= NOW() - INTERVAL '7 days';
```

<pre>
|QUERY PLAN                                                                                                          |
+--------------------------------------------------------------------------------------------------------------------+
|Aggregate  (cost=2.36..2.37 rows=1 width=4) (actual time=0.030..0.031 rows=1 loops=1)                               |
|  ->  Hash Join  (cost=1.21..2.35 rows=3 width=4) (actual time=0.025..0.028 rows=7 loops=1)                         |
|        Hash Cond: (t.session_id = s.id)                                                                            |
|        ->  Seq Scan on tickets t  (cost=0.00..1.10 rows=10 width=12) (actual time=0.009..0.010 rows=10 loops=1)    |
|        ->  Hash  (cost=1.18..1.18 rows=3 width=8) (actual time=0.010..0.011 rows=7 loops=1)                        |
|              Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                          |
|              ->  Seq Scan on sessions s  (cost=0.00..1.18 rows=3 width=8) (actual time=0.006..0.008 rows=7 loops=1)|
|                    Filter: (session_start >= (now() - '7 days'::interval))                                         |
|                    Rows Removed by Filter: 4                                                                       |
|Planning Time: 0.095 ms                                                                                             |
|Execution Time: 0.049 ms                                                                                            |
</pre>


### План на БД до 10000 строк
<pre>
|QUERY PLAN                                                                                                                   |
+-----------------------------------------------------------------------------------------------------------------------------+
|Aggregate  (cost=462.36..462.37 rows=1 width=4) (actual time=4.210..4.212 rows=1 loops=1)                                    |
|  ->  Hash Join  (cost=393.99..454.86 rows=3000 width=4) (actual time=3.320..4.083 rows=3000 loops=1)                        |
|        Hash Cond: (t.session_id = s.id)                                                                                     |
|        ->  Seq Scan on tickets t  (cost=0.00..53.00 rows=3000 width=12) (actual time=0.007..0.153 rows=3000 loops=1)        |
|        ->  Hash  (cost=269.00..269.00 rows=9999 width=8) (actual time=3.289..3.290 rows=10000 loops=1)                      |
|              Buckets: 16384  Batches: 1  Memory Usage: 519kB                                                                |
|              ->  Seq Scan on sessions s  (cost=0.00..269.00 rows=9999 width=8) (actual time=0.006..2.162 rows=10000 loops=1)|
|                    Filter: (session_start >= (now() - '7 days'::interval))                                                  |
|Planning Time: 0.388 ms                                                                                                      |
|Execution Time: 4.237 ms                                                                                                     |
</pre>

### План на БД до 10000000 строк
<pre>
QUERY PLAN                                                                                                                                        |
--------------------------------------------------------------------------------------------------------------------------------------------------+
Finalize Aggregate  (cost=15483.31..15483.32 rows=1 width=4) (actual time=189.424..192.846 rows=1 loops=1)                                        |
  ->  Gather  (cost=15483.09..15483.30 rows=2 width=4) (actual time=189.102..192.837 rows=3 loops=1)                                              |
        Workers Planned: 2                                                                                                                        |
        Workers Launched: 2                                                                                                                       |
        ->  Partial Aggregate  (cost=14483.09..14483.10 rows=1 width=4) (actual time=182.847..182.861 rows=1 loops=3)                             |
              ->  Hash Join  (cost=786.98..13438.41 rows=417875 width=4) (actual time=7.449..167.889 rows=334333 loops=3)                         |
                    Hash Cond: (t.session_id = s.id)                                                                                              |
                    ->  Parallel Seq Scan on tickets t  (cost=0.00..11554.17 rows=417917 width=12) (actual time=0.042..60.071 rows=334333 loops=3)|
                    ->  Hash  (cost=537.00..537.00 rows=19998 width=8) (actual time=7.045..7.046 rows=20000 loops=3)                              |
                          Buckets: 32768  Batches: 1  Memory Usage: 1038kB                                                                        |
                          ->  Seq Scan on sessions s  (cost=0.00..537.00 rows=19998 width=8) (actual time=0.033..4.363 rows=20000 loops=3)        |
                                Filter: (session_start >= (now() - '7 days'::interval))                                                           |
Planning Time: 1.579 ms                                                                                                                           |
Execution Time: 193.190 ms                                                                                                                        |
</pre>

### План на БД до 10000000 строк, что удалось улучшить
<pre>
QUERY PLAN                                                                                                                                        |
--------------------------------------------------------------------------------------------------------------------------------------------------+
Finalize Aggregate  (cost=15483.44..15483.45 rows=1 width=4) (actual time=123.146..126.291 rows=1 loops=1)                                        |
  ->  Gather  (cost=15483.22..15483.43 rows=2 width=4) (actual time=122.911..126.285 rows=3 loops=1)                                              |
        Workers Planned: 2                                                                                                                        |
        Workers Launched: 2                                                                                                                       |
        ->  Partial Aggregate  (cost=14483.22..14483.23 rows=1 width=4) (actual time=119.301..119.307 rows=1 loops=3)                             |
              ->  Hash Join  (cost=787.00..13438.43 rows=417917 width=4) (actual time=9.543..104.772 rows=334333 loops=3)                         |
                    Hash Cond: (t.session_id = s.id)                                                                                              |
                    ->  Parallel Seq Scan on tickets t  (cost=0.00..11554.17 rows=417917 width=12) (actual time=0.007..21.114 rows=334333 loops=3)|
                    ->  Hash  (cost=537.00..537.00 rows=20000 width=8) (actual time=9.093..9.094 rows=20000 loops=3)                              |
                          Buckets: 32768  Batches: 1  Memory Usage: 1038kB                                                                        |
                          ->  Seq Scan on sessions s  (cost=0.00..537.00 rows=20000 width=8) (actual time=0.029..5.192 rows=20000 loops=3)        |
                                Filter: (session_start >= (now() - '7 days'::interval))                                                           |
Planning Time: 1.021 ms                                                                                                                           |
Execution Time: 126.448 ms                                                                                                                        |
</pre>

### Перечень оптимизаций с пояснениями
Индекс для session_start уже был добавлен для других запросов. 
Так же для этого запроса добавил индекс для sessions.id чтобы ускорить `JOIN sessions s ON t.session_id = s.id`
```sql
CREATE INDEX idx_sessions_id ON public.sessions(id);
```

---


## 5. Поиск 3 самых прибыльных фильмов за неделю
```sql
EXPLAIN ANALYZE SELECT
    f.title,
    SUM(t.final_price) AS total_revenue
FROM
    tickets t
JOIN
    sessions s ON t.session_id = s.id
JOIN
    films f ON s.film_id = f.id
WHERE
    s.session_start >= NOW() - INTERVAL '7 days'
GROUP BY
    f.title
ORDER BY
    total_revenue DESC
LIMIT 3;
```

<pre>
|QUERY PLAN                                                                                                                                         |
+---------------------------------------------------------------------------------------------------------------------------------------------------+
|Limit  (cost=3.65..3.66 rows=3 width=2008) (actual time=0.597..0.599 rows=3 loops=1)                                                               |
|  ->  Sort  (cost=3.65..3.66 rows=3 width=2008) (actual time=0.596..0.598 rows=3 loops=1)                                                          |
|        Sort Key: (sum(t.final_price)) DESC                                                                                                        |
|        Sort Method: quicksort  Memory: 30kB                                                                                                       |
|        ->  GroupAggregate  (cost=3.58..3.63 rows=3 width=2008) (actual time=0.486..0.493 rows=5 loops=1)                                          |
|              Group Key: f.title                                                                                                                   |
|              ->  Sort  (cost=3.58..3.59 rows=3 width=2008) (actual time=0.482..0.484 rows=7 loops=1)                                              |
|                    Sort Key: f.title                                                                                                              |
|                    Sort Method: quicksort  Memory: 28kB                                                                                           |
|                    ->  Hash Join  (cost=2.39..3.55 rows=3 width=2008) (actual time=0.274..0.277 rows=7 loops=1)                                   |
|                          Hash Cond: (f.id = s.film_id)                                                                                            |
|                          ->  Seq Scan on films f  (cost=0.00..1.10 rows=10 width=2012) (actual time=0.039..0.040 rows=10 loops=1)                 |
|                          ->  Hash  (cost=2.35..2.35 rows=3 width=12) (actual time=0.144..0.145 rows=7 loops=1)                                    |
|                                Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                       |
|                                ->  Hash Join  (cost=1.21..2.35 rows=3 width=12) (actual time=0.121..0.125 rows=7 loops=1)                         |
|                                      Hash Cond: (t.session_id = s.id)                                                                             |
|                                      ->  Seq Scan on tickets t  (cost=0.00..1.10 rows=10 width=12) (actual time=0.014..0.014 rows=10 loops=1)     |
|                                      ->  Hash  (cost=1.18..1.18 rows=3 width=16) (actual time=0.033..0.033 rows=7 loops=1)                        |
|                                            Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                           |
|                                            ->  Seq Scan on sessions s  (cost=0.00..1.18 rows=3 width=16) (actual time=0.008..0.011 rows=7 loops=1)|
|                                                  Filter: (session_start >= (now() - '7 days'::interval))                                          |
|                                                  Rows Removed by Filter: 4                                                                        |
|Planning Time: 0.349 ms                                                                                                                            |
|Execution Time: 0.760 ms                                                                                                                           |
</pre>

### План на БД до 10000 строк
<pre>
|QUERY PLAN                                                                                                                                      |
+------------------------------------------------------------------------------------------------------------------------------------------------+
|Limit  (cost=494.33..494.34 rows=3 width=508) (actual time=7.050..7.053 rows=3 loops=1)                                                         |
|  ->  Sort  (cost=494.33..494.68 rows=140 width=508) (actual time=7.049..7.052 rows=3 loops=1)                                                  |
|        Sort Key: (sum(t.final_price)) DESC                                                                                                     |
|        Sort Method: top-N heapsort  Memory: 31kB                                                                                               |
|        ->  HashAggregate  (cost=491.12..492.52 rows=140 width=508) (actual time=7.024..7.032 rows=50 loops=1)                                  |
|              Group Key: f.title                                                                                                                |
|              Batches: 1  Memory Usage: 96kB                                                                                                    |
|              ->  Hash Join  (cost=407.14..476.12 rows=3000 width=508) (actual time=3.527..4.865 rows=3000 loops=1)                             |
|                    Hash Cond: (s.film_id = f.id)                                                                                               |
|                    ->  Hash Join  (cost=393.99..454.86 rows=3000 width=12) (actual time=3.480..4.386 rows=3000 loops=1)                        |
|                          Hash Cond: (t.session_id = s.id)                                                                                      |
|                          ->  Seq Scan on tickets t  (cost=0.00..53.00 rows=3000 width=12) (actual time=0.003..0.178 rows=3000 loops=1)         |
|                          ->  Hash  (cost=269.00..269.00 rows=9999 width=16) (actual time=3.462..3.462 rows=10000 loops=1)                      |
|                                Buckets: 16384  Batches: 1  Memory Usage: 597kB                                                                 |
|                                ->  Seq Scan on sessions s  (cost=0.00..269.00 rows=9999 width=16) (actual time=0.004..2.192 rows=10000 loops=1)|
|                                      Filter: (session_start >= (now() - '7 days'::interval))                                                   |
|                    ->  Hash  (cost=11.40..11.40 rows=140 width=512) (actual time=0.043..0.043 rows=50 loops=1)                                 |
|                          Buckets: 1024  Batches: 1  Memory Usage: 35kB                                                                         |
|                          ->  Seq Scan on films f  (cost=0.00..11.40 rows=140 width=512) (actual time=0.019..0.027 rows=50 loops=1)             |
|Planning Time: 0.469 ms                                                                                                                         |
|Execution Time: 7.105 ms                                                                                                                        |
</pre>

### План на БД до 10000000 строк
<pre>
QUERY PLAN                                                                                                                                                                |
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Limit  (cost=17714.76..17714.76 rows=3 width=508) (actual time=413.338..417.343 rows=3 loops=1)                                                                           |
  ->  Sort  (cost=17714.76..17715.11 rows=140 width=508) (actual time=413.336..417.340 rows=3 loops=1)                                                                    |
        Sort Key: (sum(t.final_price)) DESC                                                                                                                               |
        Sort Method: top-N heapsort  Memory: 31kB                                                                                                                         |
        ->  Finalize GroupAggregate  (cost=17677.48..17712.95 rows=140 width=508) (actual time=412.995..417.258 rows=50 loops=1)                                          |
              Group Key: f.title                                                                                                                                          |
              ->  Gather Merge  (cost=17677.48..17710.15 rows=280 width=508) (actual time=412.979..417.100 rows=150 loops=1)                                              |
                    Workers Planned: 2                                                                                                                                    |
                    Workers Launched: 2                                                                                                                                   |
                    ->  Sort  (cost=16677.45..16677.80 rows=140 width=508) (actual time=409.391..409.401 rows=50 loops=3)                                                 |
                          Sort Key: f.title                                                                                                                               |
                          Sort Method: quicksort  Memory: 50kB                                                                                                            |
                          Worker 0:  Sort Method: quicksort  Memory: 50kB                                                                                                 |
                          Worker 1:  Sort Method: quicksort  Memory: 50kB                                                                                                 |
                          ->  Partial HashAggregate  (cost=16671.06..16672.46 rows=140 width=508) (actual time=404.308..404.327 rows=50 loops=3)                          |
                                Group Key: f.title                                                                                                                        |
                                Batches: 1  Memory Usage: 96kB                                                                                                            |
                                Worker 0:  Batches: 1  Memory Usage: 96kB                                                                                                 |
                                Worker 1:  Batches: 1  Memory Usage: 96kB                                                                                                 |
                                ->  Hash Join  (cost=800.12..14581.69 rows=417875 width=508) (actual time=7.900..162.557 rows=334333 loops=3)                             |
                                      Hash Cond: (s.film_id = f.id)                                                                                                       |
                                      ->  Hash Join  (cost=786.98..13438.41 rows=417875 width=12) (actual time=7.803..114.441 rows=334333 loops=3)                        |
                                            Hash Cond: (t.session_id = s.id)                                                                                              |
                                            ->  Parallel Seq Scan on tickets t  (cost=0.00..11554.17 rows=417917 width=12) (actual time=0.005..19.563 rows=334333 loops=3)|
                                            ->  Hash  (cost=537.00..537.00 rows=19998 width=16) (actual time=7.649..7.650 rows=20000 loops=3)                             |
                                                  Buckets: 32768  Batches: 1  Memory Usage: 1194kB                                                                        |
                                                  ->  Seq Scan on sessions s  (cost=0.00..537.00 rows=19998 width=16) (actual time=0.032..4.585 rows=20000 loops=3)       |
                                                        Filter: (session_start >= (now() - '7 days'::interval))                                                           |
                                      ->  Hash  (cost=11.40..11.40 rows=140 width=512) (actual time=0.072..0.073 rows=50 loops=3)                                         |
                                            Buckets: 1024  Batches: 1  Memory Usage: 35kB                                                                                 |
                                            ->  Seq Scan on films f  (cost=0.00..11.40 rows=140 width=512) (actual time=0.010..0.048 rows=50 loops=3)                     |
Planning Time: 0.892 ms                                                                                                                                                   |
Execution Time: 417.665 ms                                                                                                                                                |
</pre>

### План на БД до 10000000 строк, что удалось улучшить
<pre>
QUERY PLAN                                                                                                                                                                |
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Limit  (cost=17715.10..17715.11 rows=3 width=508) (actual time=372.501..376.075 rows=3 loops=1)                                                                           |
  ->  Sort  (cost=17715.10..17715.45 rows=140 width=508) (actual time=372.499..376.071 rows=3 loops=1)                                                                    |
        Sort Key: (sum(t.final_price)) DESC                                                                                                                               |
        Sort Method: top-N heapsort  Memory: 31kB                                                                                                                         |
        ->  Finalize GroupAggregate  (cost=17677.82..17713.29 rows=140 width=508) (actual time=372.058..375.967 rows=50 loops=1)                                          |
              Group Key: f.title                                                                                                                                          |
              ->  Gather Merge  (cost=17677.82..17710.49 rows=280 width=508) (actual time=372.039..375.765 rows=150 loops=1)                                              |
                    Workers Planned: 2                                                                                                                                    |
                    Workers Launched: 2                                                                                                                                   |
                    ->  Sort  (cost=16677.80..16678.15 rows=140 width=508) (actual time=368.645..368.655 rows=50 loops=3)                                                 |
                          Sort Key: f.title                                                                                                                               |
                          Sort Method: quicksort  Memory: 50kB                                                                                                            |
                          Worker 0:  Sort Method: quicksort  Memory: 50kB                                                                                                 |
                          Worker 1:  Sort Method: quicksort  Memory: 50kB                                                                                                 |
                          ->  Partial HashAggregate  (cost=16671.41..16672.81 rows=140 width=508) (actual time=368.382..368.396 rows=50 loops=3)                          |
                                Group Key: f.title                                                                                                                        |
                                Batches: 1  Memory Usage: 96kB                                                                                                            |
                                Worker 0:  Batches: 1  Memory Usage: 96kB                                                                                                 |
                                Worker 1:  Batches: 1  Memory Usage: 96kB                                                                                                 |
                                ->  Hash Join  (cost=800.15..14581.82 rows=417917 width=508) (actual time=7.891..152.433 rows=334333 loops=3)                             |
                                      Hash Cond: (s.film_id = f.id)                                                                                                       |
                                      ->  Hash Join  (cost=787.00..13438.43 rows=417917 width=12) (actual time=7.779..107.636 rows=334333 loops=3)                        |
                                            Hash Cond: (t.session_id = s.id)                                                                                              |
                                            ->  Parallel Seq Scan on tickets t  (cost=0.00..11554.17 rows=417917 width=12) (actual time=0.007..17.775 rows=334333 loops=3)|
                                            ->  Hash  (cost=537.00..537.00 rows=20000 width=16) (actual time=7.607..7.608 rows=20000 loops=3)                             |
                                                  Buckets: 32768  Batches: 1  Memory Usage: 1194kB                                                                        |
                                                  ->  Seq Scan on sessions s  (cost=0.00..537.00 rows=20000 width=16) (actual time=0.027..4.545 rows=20000 loops=3)       |
                                                        Filter: (session_start >= (now() - '7 days'::interval))                                                           |
                                      ->  Hash  (cost=11.40..11.40 rows=140 width=512) (actual time=0.071..0.072 rows=50 loops=3)                                         |
                                            Buckets: 1024  Batches: 1  Memory Usage: 35kB                                                                                 |
                                            ->  Seq Scan on films f  (cost=0.00..11.40 rows=140 width=512) (actual time=0.022..0.047 rows=50 loops=3)                     |
Planning Time: 0.707 ms                                                                                                                                                   |
Execution Time: 376.448 ms                                                                                                                                                |
</pre>

### Перечень оптимизаций с пояснениями
Ускорение для `JOIN films f ON s.film_id = f.id`
```sql
CREATE INDEX idx_sessions_film_id ON public.sessions(film_id);
```

---



## 6 Свободные и занятые места на конкретный сеанс
```sql
EXPLAIN ANALYZE SELECT 
    s.id AS seat_id,
    s.seat_row,
    s.seat_number,
    CASE 
        WHEN t.id IS NOT NULL THEN true
        ELSE false
    END AS is_sold
FROM 
    seats s
JOIN 
    sessions sess ON sess.hall_id = s.hall_id
LEFT JOIN 
    tickets t ON t.seat_id = s.id AND t.session_id = sess.id
WHERE 
    sess.id = 1
ORDER BY 
    s.seat_row, s.seat_number;
```

<pre>
|QUERY PLAN                                                                                                                    |
+------------------------------------------------------------------------------------------------------------------------------+
|Sort  (cost=3.42..3.43 rows=1 width=17) (actual time=0.117..0.119 rows=6 loops=1)                                             |
|  Sort Key: s.seat_row, s.seat_number                                                                                         |
|  Sort Method: quicksort  Memory: 25kB                                                                                        |
|  ->  Nested Loop Left Join  (cost=1.14..3.41 rows=1 width=17) (actual time=0.091..0.111 rows=6 loops=1)                      |
|        Join Filter: (t.seat_id = s.id)                                                                                       |
|        Rows Removed by Join Filter: 10                                                                                       |
|        ->  Hash Join  (cost=1.14..2.27 rows=1 width=24) (actual time=0.084..0.089 rows=6 loops=1)                            |
|              Hash Cond: (s.hall_id = sess.hall_id)                                                                           |
|              ->  Seq Scan on seats s  (cost=0.00..1.10 rows=10 width=24) (actual time=0.037..0.038 rows=10 loops=1)          |
|              ->  Hash  (cost=1.12..1.12 rows=1 width=16) (actual time=0.014..0.014 rows=1 loops=1)                           |
|                    Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                              |
|                    ->  Seq Scan on sessions sess  (cost=0.00..1.12 rows=1 width=16) (actual time=0.010..0.011 rows=1 loops=1)|
|                          Filter: (id = 1)                                                                                    |
|                          Rows Removed by Filter: 10                                                                          |
|        ->  Seq Scan on tickets t  (cost=0.00..1.12 rows=1 width=24) (actual time=0.001..0.002 rows=2 loops=6)                |
|              Filter: (session_id = 1)                                                                                        |
|              Rows Removed by Filter: 8                                                                                       |
|Planning Time: 0.158 ms                                                                                                       |
|Execution Time: 0.149 ms                                                                                                      |
</pre>

### План на БД до 10000 строк
<pre>
|QUERY PLAN                                                                                                                                        |
+--------------------------------------------------------------------------------------------------------------------------------------------------+
|Sort  (cost=101.20..101.53 rows=131 width=17) (actual time=0.516..0.524 rows=196 loops=1)                                                         |
|  Sort Key: s.seat_row, s.seat_number                                                                                                             |
|  Sort Method: quicksort  Memory: 32kB                                                                                                            |
|  ->  Hash Left Join  (cost=68.83..96.59 rows=131 width=17) (actual time=0.217..0.392 rows=196 loops=1)                                           |
|        Hash Cond: (s.id = t.seat_id)                                                                                                             |
|        ->  Hash Join  (cost=8.31..35.58 rows=131 width=24) (actual time=0.044..0.191 rows=196 loops=1)                                           |
|              Hash Cond: (s.hall_id = sess.hall_id)                                                                                               |
|              ->  Seq Scan on seats s  (cost=0.00..22.52 rows=1252 width=24) (actual time=0.006..0.065 rows=1252 loops=1)                         |
|              ->  Hash  (cost=8.30..8.30 rows=1 width=16) (actual time=0.019..0.020 rows=1 loops=1)                                               |
|                    Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                  |
|                    ->  Index Scan using sessions_pk on sessions sess  (cost=0.29..8.30 rows=1 width=16) (actual time=0.015..0.015 rows=1 loops=1)|
|                          Index Cond: (id = 1)                                                                                                    |
|        ->  Hash  (cost=60.50..60.50 rows=1 width=24) (actual time=0.170..0.170 rows=0 loops=1)                                                   |
|              Buckets: 1024  Batches: 1  Memory Usage: 8kB                                                                                        |
|              ->  Seq Scan on tickets t  (cost=0.00..60.50 rows=1 width=24) (actual time=0.169..0.170 rows=0 loops=1)                             |
|                    Filter: (session_id = 1)                                                                                                      |
|                    Rows Removed by Filter: 3000                                                                                                  |
|Planning Time: 0.326 ms                                                                                                                           |
|Execution Time: 0.553 ms                                                                                                                          |
</pre>

### План на БД до 10000000 строк
<pre>
QUERY PLAN                                                                                                                                              |
--------------------------------------------------------------------------------------------------------------------------------------------------------+
Sort  (cost=14321.09..14322.00 rows=364 width=17) (actual time=28.271..30.998 rows=496 loops=1)                                                         |
  Sort Key: s.seat_row, s.seat_number                                                                                                                   |
  Sort Method: quicksort  Memory: 44kB                                                                                                                  |
  ->  Hash Right Join  (cost=1701.35..14305.61 rows=364 width=17) (actual time=5.186..30.775 rows=496 loops=1)                                          |
        Hash Cond: (t.seat_id = s.id)                                                                                                                   |
        ->  Gather  (cost=1000.00..13604.06 rows=51 width=24) (actual time=0.797..26.224 rows=65 loops=1)                                               |
              Workers Planned: 2                                                                                                                        |
              Workers Launched: 2                                                                                                                       |
              ->  Parallel Seq Scan on tickets t  (cost=0.00..12598.96 rows=21 width=24) (actual time=0.885..20.736 rows=22 loops=3)                    |
                    Filter: (session_id = 1)                                                                                                            |
                    Rows Removed by Filter: 334312                                                                                                      |
        ->  Hash  (cost=696.80..696.80 rows=364 width=24) (actual time=4.380..4.384 rows=496 loops=1)                                                   |
              Buckets: 1024  Batches: 1  Memory Usage: 36kB                                                                                             |
              ->  Hash Join  (cost=8.32..696.80 rows=364 width=24) (actual time=0.059..4.295 rows=496 loops=1)                                          |
                    Hash Cond: (s.hall_id = sess.hall_id)                                                                                               |
                    ->  Seq Scan on seats s  (cost=0.00..594.52 rows=34252 width=24) (actual time=0.010..1.779 rows=34252 loops=1)                      |
                    ->  Hash  (cost=8.30..8.30 rows=1 width=16) (actual time=0.016..0.018 rows=1 loops=1)                                               |
                          Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                  |
                          ->  Index Scan using sessions_pk on sessions sess  (cost=0.29..8.30 rows=1 width=16) (actual time=0.013..0.014 rows=1 loops=1)|
                                Index Cond: (id = 1)                                                                                                    |
Planning Time: 0.317 ms                                                                                                                                 |
Execution Time: 31.074 ms                                                                                                                               |
</pre>

### План на БД до 10000000 строк, что удалось улучшить
<pre>
QUERY PLAN                                                                                                                                            |
------------------------------------------------------------------------------------------------------------------------------------------------------+
Sort  (cost=504.27..505.18 rows=364 width=17) (actual time=2.765..2.786 rows=496 loops=1)                                                             |
  Sort Key: s.seat_row, s.seat_number                                                                                                                 |
  Sort Method: quicksort  Memory: 44kB                                                                                                                |
  ->  Hash Right Join  (cost=296.67..488.78 rows=364 width=17) (actual time=2.480..2.635 rows=496 loops=1)                                            |
        Hash Cond: (t.seat_id = s.id)                                                                                                                 |
        ->  Bitmap Heap Scan on tickets t  (cost=4.82..196.73 rows=51 width=24) (actual time=0.022..0.102 rows=65 loops=1)                            |
              Recheck Cond: (session_id = 1)                                                                                                          |
              Heap Blocks: exact=65                                                                                                                   |
              ->  Bitmap Index Scan on idx_tickets_session_id  (cost=0.00..4.81 rows=51 width=0) (actual time=0.011..0.012 rows=65 loops=1)           |
                    Index Cond: (session_id = 1)                                                                                                      |
        ->  Hash  (cost=287.30..287.30 rows=364 width=24) (actual time=2.431..2.434 rows=496 loops=1)                                                 |
              Buckets: 1024  Batches: 1  Memory Usage: 36kB                                                                                           |
              ->  Nested Loop  (cost=6.99..287.30 rows=364 width=24) (actual time=0.173..2.323 rows=496 loops=1)                                      |
                    ->  Index Scan using idx_sessions_id on sessions sess  (cost=0.29..8.30 rows=1 width=16) (actual time=0.025..0.027 rows=1 loops=1)|
                          Index Cond: (id = 1)                                                                                                        |
                    ->  Bitmap Heap Scan on seats s  (cost=6.70..275.88 rows=311 width=24) (actual time=0.108..2.172 rows=496 loops=1)                |
                          Recheck Cond: (hall_id = sess.hall_id)                                                                                      |
                          Heap Blocks: exact=246                                                                                                      |
                          ->  Bitmap Index Scan on idx_seats_hall_id  (cost=0.00..6.62 rows=311 width=0) (actual time=0.075..0.075 rows=496 loops=1)  |
                                Index Cond: (hall_id = sess.hall_id)                                                                                  |
Planning Time: 1.113 ms                                                                                                                               |
Execution Time: 2.901 ms                                                                                                                              |
</pre>

### Перечень оптимизаций с пояснениями
Запрос многотабличный поэтому индексов несколько

Ускорение `LEFT JOIN tickets t ON t.seat_id = s.id`
```sql
CREATE INDEX idx_seats_id ON public.seats(id);
```

Ускорение `JOIN sessions sess ON sess.hall_id = s.hall_id`
```sql
CREATE INDEX idx_seats_hall_id ON public.seats(hall_id);
```

Композитный индекс для ускорения `LEFT JOIN tickets t ON t.seat_id = s.id AND t.session_id = sess.id`.  Композитный так в JOIN используется AND
```sql
CREATE INDEX idx_tickets_seat_session ON public.tickets(seat_id, session_id);
```

Композитный индекс для ускорения `ORDER BY s.seat_row, s.seat_number`.  
```sql
CREATE INDEX idx_seats_row_number ON public.seats(seat_row, seat_number);
```
