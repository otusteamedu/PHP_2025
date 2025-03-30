### 1. Выбор всех фильмов на сегодня

**SQL-запрос:**

```sql
SELECT DISTINCT film.*
FROM film
         JOIN public.session s on film.id = s.film_id
WHERE DATE (s.start_from) = CURRENT_DATE
;
```

**Индексы:**

```sql
-- таблица сеансов
-- Для ускорения фильтрации по дате начала и конца сеанса
CREATE INDEX IF NOT EXISTS "idx_session_start_from_date" ON session USING BRIN (DATE (start_from));
CREATE INDEX IF NOT EXISTS "idx_session_start_from_date" ON session USING BRIN (DATE (end_to));
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx_session_film_id" ON session (film_id);
CREATE INDEX IF NOT EXISTS "idx_session_rating_id" ON session (rating_id);
CREATE INDEX IF NOT EXISTS "idx_session_cinema_room_id" ON session (cinema_room_id);
```

|                    | 10k            | 10M                  | 10M indexed          | Result            |
|--------------------|:---------------|:---------------------|:---------------------|:------------------|
| **Cost**           | 324.40..325.03 | 208753.41..209142.09 | 208753.04..209141.71 |                   |
| **Execution Time** | 2.977 ms       | 750.485 ms           | 380.324 ms           | reduction by 1.97 |

### 2. Подсчёт проданных билетов за неделю

**SQL-запрос:**

```sql
SELECT *
FROM ticket
         JOIN public.session s on s.id = ticket.session_id
WHERE DATE (s.start_from) <= CURRENT_DATE
  AND DATE (s.start_from)
    >= CURRENT_DATE - INTERVAL '7 days'
;
```

**Индексы:**

```sql
-- таблица билетов
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx_ticket_session_id" ON ticket (session_id);
CREATE INDEX IF NOT EXISTS "idx_ticket_seat_id" ON ticket (seat_id);
-- Для подсчета стоимости билетов
CREATE INDEX IF NOT EXISTS "idx_ticket_price" ON ticket (price);
```

|                    | 10k           | 10M               | 10M indexed       | Result            |
|--------------------|:--------------|:------------------|:------------------|:------------------|
| **Cost**           | 4.58..3902.38 | 1000.45..32565.59 | 1000.44..28751.05 |                   |
| **Execution Time** | 1.436 ms      | 403.086 ms        | 197.229 ms        | reduction by 2.04 |

### 3. Формирование афиши (фильмы, которые показывают сегодня)

**SQL-запрос:**

```sql
SELECT DISTINCT film.*
FROM film
         JOIN public.session s on film.id = s.film_id
WHERE DATE (s.start_from) = CURRENT_DATE
;
```

**Индексы:**

```sql
-- таблица сеансов
-- Для ускорения фильтрации по дате начала и конца сеанса
CREATE INDEX IF NOT EXISTS "idx_session_start_from_date" ON session USING BRIN (DATE (start_from));
CREATE INDEX IF NOT EXISTS "idx_session_start_from_date" ON session USING BRIN (DATE (end_to));
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx_session_film_id" ON session (film_id);
CREATE INDEX IF NOT EXISTS "idx_session_rating_id" ON session (rating_id);
CREATE INDEX IF NOT EXISTS "idx_session_cinema_room_id" ON session (cinema_room_id);
```

|                    | 10k            | 10M                  | 10M indexed          | Result            |
|--------------------|:---------------|:---------------------|:---------------------|:------------------|
| **Cost**           | 324.40..325.03 | 208764.57..210364.67 | 208764.20..210364.22 |                   |
| **Execution Time** | 2.977 ms       | 976.692 ms           | 410.529 ms           | reduction by 2.37 |

### 4. Поиск 3 самых прибыльных фильмов за неделю

**SQL-запрос:**

```sql
SELECT SUM(t.price) AS sum_price, f.id
FROM ticket t
         JOIN public.session s on s.id = t.session_id
         JOIN public.film f on f.id = s.film_id
WHERE DATE (s.start_from) >= CURRENT_DATE
  AND DATE (s.start_from)
    < CURRENT_DATE - INTERVAL '7 days'
GROUP BY f.id
ORDER BY sum_price DESC
    LIMIT 3
;
```

**Индексы:**

```sql
-- таблица билетов
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx_ticket_session_id" ON ticket (session_id);
CREATE INDEX IF NOT EXISTS "idx_session_film_id" ON session (film_id);
-- Для подсчета стоимости билетов
CREATE INDEX IF NOT EXISTS "idx_ticket_price" ON ticket (price);
```

|                    | 10k              | 10M                | 10M indexed        | Result            |
|--------------------|:-----------------|:-------------------|:-------------------|:------------------|
| **Cost**           | 3934.06..3934.07 | 32552.33..32552.34 | 29604.56..29604.57 | -                 |
| **Execution Time** | 1.123 ms         | 538.195 ms         | 67.870 ms          | reduction by 7.92 |

### 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

**SQL-запрос:**

```sql
SELECT cr.title,
       crs.row,
       crs.place,
       CASE
           WHEN t.id is null THEN true
           ELSE false
           END AS is_free
FROM session s
         JOIN public.cinema_room cr on cr.id = s.cinema_room_id
         JOIN public.cinema_room_seat crs on cr.id = crs.cinema_room_id
         LEFT JOIN public.ticket t on crs.id = t.seat_id AND t.session_id = '09f55d7f-b5bd-44bd-b6d0-e7bce12d8e0f'
WHERE s.id = '09f55d7f-b5bd-44bd-b6d0-e7bce12d8e0f'
ORDER BY crs.row, crs.place
;
```

**Индексы:**

```sql
-- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx_session_cinema_room_id" ON session (cinema_room_id);
CREATE INDEX IF NOT EXISTS "idx_cinema_room_seat_cinema_room_id" ON cinema_room_seat (cinema_room_id);
```

|                    | 10k            | 10M            | 10M indexed  | Result              |
|--------------------|:---------------|:---------------|:-------------|:--------------------|
| **Cost**           | 214.56..215.60 | 124.28..124.35 | 93.99..94.06 |                     |
| **Execution Time** | 5.114 ms       | 427.921 ms     | 1.876 ms     | reduction by 228.10 |

### 6. Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс

**SQL-запрос:**

```sql
SELECT MIN(price) AS min_price, MAX(price) AS max_price
FROM ticket
WHERE session_id = '5c964475-6259-4ab8-a684-b1e61ceb718c'
;
```

**Индексы:**

```sql
-- Для подсчета стоимости билетов
CREATE INDEX IF NOT EXISTS "idx_ticket_price" ON ticket (price);
```

|                    | 10k          | 10M          | 10M indexed  | Result              |
|--------------------|:-------------|:-------------|:-------------|:--------------------|
| **Cost**           | 79.04..79.05 | 73.54..73.55 | 73.41..73.42 |                     |
| **Execution Time** | 5.168 ms     | 300.138 ms   | 2.099 ms     | reduction by 142.99 |

### -- 5 самых часто используемых индексов

```sql
SELECT *
FROM pg_stat_all_indexes
WHERE schemaname = 'public'
ORDER BY idx_scan DESC LIMIT 5;
```

| relid | indexrelid | schemaname | relname            | indexrelname             | idx\_scan | last\_idx\_scan                   | idx\_tup\_read | idx\_tup\_fetch |
|:------|:-----------|:-----------|:-------------------|:-------------------------|:----------|:----------------------------------|:---------------|:----------------|
| 16400 | 16405      | public     | cinema\_room       | cinema\_room\_pkey       | 10009759  | 2025-03-30 05:12:44.534542 +00:00 | 10009759       | 10009759        |
| 16393 | 16398      | public     | session\_rating    | session\_rating\_pkey    | 10005641  | 2025-03-30 03:19:40.227517 +00:00 | 10005641       | 10005641        |
| 16386 | 16391      | public     | film               | film\_pkey               | 10000113  | 2025-03-30 05:09:56.612520 +00:00 | 10000113       | 10000113        |
| 16407 | 16411      | public     | session            | session\_pkey            | 171516    | 2025-03-30 05:12:44.534542 +00:00 | 171516         | 171494          |
| 16430 | 16437      | public     | cinema\_room\_seat | cinema\_room\_seat\_pkey | 110454    | 2025-03-30 05:12:44.534542 +00:00 | 110454         | 110440          |

### -- 5 самых редко используемых индексов

```sql
SELECT *
FROM pg_stat_all_indexes
WHERE schemaname = 'public'
ORDER BY idx_scan ASC LIMIT 5;
```

| relid | indexrelid | schemaname | relname | indexrelname                    | idx\_scan | last\_idx\_scan | idx\_tup\_read | idx\_tup\_fetch |
|:------|:-----------|:-----------|:--------|:--------------------------------|:----------|:----------------|:---------------|:----------------|
| 16407 | 16468      | public     | session | idx\_session\_film\_id          | 0         | null            | 0              | 0               |
| 16407 | 16469      | public     | session | idx\_session\_rating\_id        | 0         | null            | 0              | 0               |
| 16446 | 16450      | public     | ticket  | ticket\_pkey                    | 0         | null            | 0              | 0               |
| 16407 | 16467      | public     | session | idx\_session\_start\_from\_date | 0         | null            | 0              | 0               |
| 16407 | 16470      | public     | session | idx\_session\_cinema\_room\_id  | 0         | null            | 0              | 0               |


### 15 самых больших по размеру объектов БД

```sql
SELECT
    table_name,
    pg_size_pretty(table_size) AS table_size,
    pg_size_pretty(indexes_size) AS indexes_size,
    pg_size_pretty(total_size) AS total_size
FROM (
         SELECT
             table_name,
             pg_table_size(table_name) AS table_size,
             pg_indexes_size(table_name) AS indexes_size,
             pg_total_relation_size(table_name) AS total_size
         FROM (
                  SELECT ('"' || table_schema || '"."' || table_name || '"') AS table_name
                  FROM information_schema.tables
              ) AS all_tables
         ORDER BY total_size DESC
     ) AS pretty_sizes
    LIMIT 15;
```
| table\_name                     | table\_size | indexes\_size | total\_size |
|:--------------------------------|:------------|:--------------|:------------|
| "public"."session"              | 1042 MB     | 585 MB        | 1627 MB     |
| "public"."ticket"               | 9144 kB     | 16 MB         | 25 MB       |
| "pg\_catalog"."pg\_proc"        | 880 kB      | 352 kB        | 1232 kB     |
| "public"."cinema\_room\_seat"   | 312 kB      | 456 kB        | 768 kB      |
| "pg\_catalog"."pg\_rewrite"     | 696 kB      | 32 kB         | 728 kB      |
| "pg\_catalog"."pg\_attribute"   | 480 kB      | 216 kB        | 696 kB      |
| "pg\_catalog"."pg\_description" | 400 kB      | 208 kB        | 608 kB      |
| "pg\_catalog"."pg\_statistic"   | 288 kB      | 32 kB         | 320 kB      |
| "pg\_catalog"."pg\_depend"      | 144 kB      | 152 kB        | 296 kB      |
| "pg\_catalog"."pg\_collation"   | 152 kB      | 88 kB         | 240 kB      |
