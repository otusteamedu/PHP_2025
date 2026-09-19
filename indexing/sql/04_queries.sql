-- ДЗ-9 «Индексирование данных»
-- 6 основных запросов к БД кинотеатра
--
-- «Простые» (одна таблица):       №2, №6
-- «Сложные» (связи, агрегаты):    №1, №3, №4, №5
-- №4 в формулировке ЛК идёт рядом с «простыми», но это JOIN + SUM/GROUP BY.
--
-- Параметр :sid - id реального сеанса "на сегодня", вычисляется автоматически.
-- Файл запускается через psql (нужен \gset).

-- выбор тестового сеанса на сегодня (для запросов 5 и 6)
SELECT id AS sid
FROM screenings
WHERE start_time >= date_trunc('day', now())
  AND start_time < date_trunc('day', now()) + interval '1 day'
ORDER BY id
LIMIT 1 \gset

\echo '=== 1. Выбор всех фильмов на сегодня (сложный: movies + screenings) ==='
EXPLAIN (ANALYZE, BUFFERS, TIMING)
SELECT DISTINCT m.id, m.title, m.duration_minutes
FROM movies m
         JOIN screenings s ON s.movie_id = m.id
WHERE s.start_time >= date_trunc('day', now())
  AND s.start_time < date_trunc('day', now()) + interval '1 day';

\echo '=== 2. Подсчёт проданных билетов за неделю (простой: tickets) ==='
EXPLAIN (ANALYZE, BUFFERS, TIMING)
SELECT COUNT(*) AS tickets_sold_last_week
FROM tickets
WHERE sold_at >= now() - interval '7 days';

\echo '=== 3. Формирование афиши - фильмы, которые показывают сегодня (сложный) ==='
EXPLAIN (ANALYZE, BUFFERS, TIMING)
SELECT DISTINCT m.title, s.start_time, h.name AS hall
FROM screenings s
         JOIN movies m ON m.id = s.movie_id
         JOIN halls h ON h.id = s.hall_id
WHERE s.start_time >= date_trunc('day', now())
  AND s.start_time < date_trunc('day', now()) + interval '1 day'
ORDER BY s.start_time;

\echo '=== 4. Топ-3 самых прибыльных фильмов за неделю (сложный: tickets + screenings + movies, SUM) ==='
EXPLAIN (ANALYZE, BUFFERS, TIMING)
SELECT m.id, m.title, SUM(t.final_price) AS total_revenue
FROM tickets t
         JOIN screenings s ON s.id = t.screening_id
         JOIN movies m ON m.id = s.movie_id
WHERE t.sold_at >= now() - interval '7 days'
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
LIMIT 3;

\echo '=== 5. Схема зала - свободные и занятые места на сеанс :sid (сложный) ==='
EXPLAIN (ANALYZE, BUFFERS, TIMING)
SELECT st.row_number,
       st.place_number,
       sc.name AS category,
       CASE WHEN t.id IS NOT NULL THEN 'Занято' ELSE 'Свободно' END AS status,
       sp.price
FROM seats st
         JOIN seat_categories sc ON sc.id = st.seat_category_id
         LEFT JOIN tickets t
                   ON t.seat_id = st.id AND t.screening_id = :sid
         JOIN screening_prices sp
                   ON sp.screening_id = :sid AND sp.seat_category_id = st.seat_category_id
WHERE st.hall_id = (SELECT hall_id FROM screenings WHERE id = :sid)
ORDER BY st.row_number, st.place_number;

\echo '=== 6. Диапазон мин/макс цены за билет на сеанс :sid (простой: tickets) ==='
EXPLAIN (ANALYZE, BUFFERS, TIMING)
SELECT MIN(final_price) AS min_price, MAX(final_price) AS max_price
FROM tickets
WHERE screening_id = :sid;
