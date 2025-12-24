-- 1. Выбор всех фильмов на сегодня
SELECT DISTINCT m.id, m.title
FROM movie m
    JOIN showtime s ON m.id = s.movie_id
WHERE s.time >= CURRENT_DATE
  AND s.time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY m.title;

-- 2. Подсчёт проданных билетов за неделю
SELECT COUNT(*) AS tickets_sold
FROM "order" o
    JOIN showtime s ON o.showtime_id = s.id
WHERE s.time >= CURRENT_DATE - INTERVAL '7 days'
  AND s.time < CURRENT_DATE + INTERVAL '1 day';

-- 3. Формирование афиши на сегодня
SELECT m.title, h.name AS hall, s.time
FROM showtime s
    JOIN movie m ON s.movie_id = m.id
    JOIN hall h ON s.hall_id = h.id
WHERE s.time >= CURRENT_DATE
  AND s.time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.time, m.title;

-- 4. Три самых прибыльных фильма за неделю
SELECT m.id, m.title, SUM(o.price) AS total_revenue
FROM movie m
    JOIN showtime s ON m.id = s.movie_id
    JOIN "order" o ON s.id = o.showtime_id
WHERE s.time >= CURRENT_DATE - INTERVAL '7 days'
  AND s.time < CURRENT_DATE + INTERVAL '1 day'
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
LIMIT 3;

-- 5. Схема зала для конкретного сеанса
SELECT
    s.id AS seat_id,
    s.row_num,
    s.seat_num,
    st.type AS seat_type,
    CASE WHEN o.id IS NULL THEN 'свободно' ELSE 'занято' END AS status
FROM seat s
    JOIN seat_type st ON s.seat_type_id = st.id
    JOIN showtime sh ON s.hall_id = sh.hall_id
    LEFT JOIN "order" o ON s.id = o.seat_id AND o.showtime_id = sh.id
WHERE sh.id = 13
ORDER BY s.row_num, s.seat_num;

-- 6. Диапазон цен на конкретный сеанс
SELECT
    MIN(st.price) AS min_price,
    MAX(st.price) AS max_price
FROM showtime s
    JOIN hall h ON s.hall_id = h.id
    JOIN seat se ON h.id = se.hall_id
    JOIN seat_type st ON se.seat_type_id = st.id
WHERE s.id = 13;
