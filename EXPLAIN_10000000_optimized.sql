-- Снять планы для БД 10 000 000+ строк после оптимизаций.
-- Порядок:
-- films_DDL.sql -> DML_10000000.sql -> optimizations.sql -> этот файл.

\set screening_id 1

\echo 'Q1_opt'
EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT DISTINCT s.movie_id
FROM public.screenings s
WHERE s.start_time >= CURRENT_DATE
  AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.movie_id;

\echo 'Q2_opt'
EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT COUNT(*) AS sold_tickets_last_7_days
FROM public.tickets t
WHERE t.status = 'sold'
  AND t.purchase_time >= CURRENT_DATE - INTERVAL '7 days';

\echo 'Q3_opt'
EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    m.title,
    m.genre,
    h.name,
    h.type,
    scr.start_time,
    scr.end_time,
    m.duration_min
FROM public.screenings scr
JOIN public.movies m ON m.id = scr.movie_id
JOIN public.halls h ON h.id = scr.hall_id
WHERE scr.start_time >= CURRENT_DATE
  AND scr.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY scr.start_time;

\echo 'Q4_opt'
EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    m.title,
    COUNT(*) FILTER (WHERE t.status = 'sold') AS sold_tickets,
    SUM(t.price) FILTER (WHERE t.status = 'sold') AS revenue
FROM public.tickets t
JOIN public.screenings scr ON scr.id = t.screening_id
JOIN public.movies m ON m.id = scr.movie_id
WHERE scr.start_time >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY revenue DESC NULLS LAST
LIMIT 3;

\echo 'Q5_opt'
EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    s.row_num,
    s.seat_num,
    s.seat_type,
    CASE WHEN t.id IS NULL THEN 'free' ELSE 'occupied' END AS seat_status
FROM public.seats s
JOIN public.screenings scr
  ON scr.id = :screening_id
 AND scr.hall_id = s.hall_id
LEFT JOIN public.tickets t
  ON t.screening_id = scr.id
 AND t.seat_id = s.id
 AND t.status IN ('sold', 'reserved')
ORDER BY s.row_num, s.seat_num;

\echo 'Q6_opt'
EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT MIN(t.price) AS min_price, MAX(t.price) AS max_price
FROM public.tickets t
WHERE t.screening_id = :screening_id;
