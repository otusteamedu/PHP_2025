-- region Запросы

-- 1. Выбор всех фильмов на сегодня
EXPLAIN ANALYZE
SELECT DISTINCT m.title, m.description, s.start_time, m.duration_minutes
FROM movies m
    JOIN sessions s ON m.id = s.movie_id
WHERE start_time >= CURRENT_DATE AND start_time < CURRENT_DATE + INTERVAL '1 day';

CREATE INDEX idx_sessions_date ON sessions(start_time);

-- 2. Подсчёт проданных билетов за неделю
EXPLAIN ANALYZE
SELECT COUNT(*) AS total_tickets
FROM tickets
WHERE purchase_time >= NOW() - INTERVAL '7 days';

CREATE INDEX idx_tickets_purchase_time ON tickets(purchase_time);

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
EXPLAIN ANALYZE
SELECT m.title AS movie, s.start_time AS showtime, h.title AS hall
FROM movies m
    JOIN sessions s ON m.id = s.movie_id
    JOIN halls h ON s.hall_id = h.id
WHERE start_time >= CURRENT_DATE AND start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.start_time;

CREATE INDEX idx_sessions_date ON sessions(start_time);

-- 4. Поиск 3 самых прибыльных фильмов за неделю
EXPLAIN ANALYZE
SELECT m.title, SUM(t.price) AS total_revenue
FROM tickets t
    JOIN sessions s ON t.session_id = s.id
    JOIN movies m ON s.movie_id = m.id
WHERE t.purchase_time >= NOW() - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
    LIMIT 3;

-- 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
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

CREATE INDEX idx_seats_hall_row_seat ON seats(hall_id, row_number, seat_number);

-- 6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
EXPLAIN ANALYZE
SELECT MIN(price) AS min_price, MAX(price) AS max_price
FROM tickets
WHERE session_id = 15;

CREATE INDEX idx_tickets_session_id ON tickets(session_id);

-- endregion

-- Самые большие объекты в БД (таблицы + индексы):
SELECT
    relname AS object_name,
    relkind AS object_type,
    pg_size_pretty(pg_total_relation_size(oid)) AS total_size
FROM pg_class
WHERE relkind IN ('r', 'i')
ORDER BY pg_total_relation_size(oid) DESC
LIMIT 15;

-- | object_name                                   | object_type | total_size |
-- |-----------------------------------------------|-------------|------------|
-- | tickets                                       | r           | 155 MB     |
-- | tickets_session_id_row_number_seat_number_key | i           | 39 MB      |
-- | orders                                        | r           | 36 MB      |
-- | sessions                                      | r           | 24 MB      |
-- | tickets_pkey                                  | i           | 21 MB      |
-- | idx_tickets_session_id                        | i           | 15 MB      |
-- | users                                         | r           | 15 MB      |
-- | orders_pkey                                   | i           | 11 MB      |
-- | users_email_key                               | i           | 6920 kB    |
-- | idx_tickets_purchase_time                     | i           | 6312 kB    |
-- | idx_sessions_date                             | i           | 5160 kB    |
-- | sessions_pkey                                 | i           | 4408 kB    |
-- | users_pkey                                    | i           | 2208 kB    |
-- | pg_proc                                       | r           | 1232 kB    |
-- | pg_attribute                                  | r           | 744 kB     |

-- Топ-5 самых используемых индексов:
SELECT
    relname AS index_name,
    idx_scan AS usage_count
FROM pg_stat_user_indexes
ORDER BY idx_scan DESC
LIMIT 5;

-- Топ-5 самых редко используемых индексов
SELECT
    relname AS index_name,
    idx_scan AS usage_count
FROM pg_stat_user_indexes
ORDER BY idx_scan
LIMIT 5;
