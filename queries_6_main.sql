-- 6 основных запросов к БД кинотеатра
-- Важно: запросы Q1, Q2, Q6 - простые (1 таблица), Q3, Q4, Q5 - сложные (join/aggregation).

-- Параметры для psql/pgAdmin:
-- \set screening_id 1

-- Q1 (simple): выбор всех фильмов на сегодня (по id фильма из расписания)
SELECT DISTINCT s.movie_id
FROM public.screenings s
WHERE s.start_time >= CURRENT_DATE
  AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.movie_id;

-- Q2 (simple): подсчет проданных билетов за неделю
SELECT COUNT(*) AS sold_tickets_last_7_days
FROM public.tickets t
WHERE t.status = 'sold'
  AND t.purchase_time >= CURRENT_DATE - INTERVAL '7 days';

-- Q3 (complex): формирование афиши на сегодня
SELECT
    m.title AS movie_title,
    m.genre AS genre,
    h.name AS hall_name,
    h.type AS hall_type,
    TO_CHAR(scr.start_time, 'HH24:MI') AS starts_at,
    TO_CHAR(scr.end_time, 'HH24:MI') AS ends_at,
    m.duration_min AS duration_min
FROM public.screenings scr
JOIN public.movies m ON m.id = scr.movie_id
JOIN public.halls h ON h.id = scr.hall_id
WHERE scr.start_time >= CURRENT_DATE
  AND scr.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY scr.start_time;

-- Q4 (complex): топ-3 самых прибыльных фильмов за неделю
SELECT
    m.title AS movie_title,
    COUNT(*) FILTER (WHERE t.status = 'sold') AS sold_tickets,
    SUM(t.price) FILTER (WHERE t.status = 'sold') AS revenue
FROM public.tickets t
JOIN public.screenings scr ON scr.id = t.screening_id
JOIN public.movies m ON m.id = scr.movie_id
WHERE scr.start_time >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY revenue DESC NULLS LAST
LIMIT 3;

-- Q5 (complex): схема зала (свободные/занятые места) для конкретного сеанса
-- Для psql замените :screening_id через \set screening_id 1
SELECT
    s.row_num,
    s.seat_num,
    s.seat_type,
    CASE
        WHEN t.id IS NULL THEN 'free'
        ELSE 'occupied'
    END AS seat_status
FROM public.seats s
JOIN public.screenings scr
  ON scr.id = :screening_id
 AND scr.hall_id = s.hall_id
LEFT JOIN public.tickets t
  ON t.screening_id = scr.id
 AND t.seat_id = s.id
 AND t.status IN ('sold', 'reserved')
ORDER BY s.row_num, s.seat_num;

-- Q6 (simple): диапазон минимальной и максимальной цены за билет на конкретный сеанс
SELECT
    MIN(t.price) AS min_price,
    MAX(t.price) AS max_price
FROM public.tickets t
WHERE t.screening_id = :screening_id;
