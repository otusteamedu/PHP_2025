-- ЗАПРОСЫ К БД КИНОТЕАТРА

-- 1. Выбор всех фильмов на сегодня
SELECT id, title, duration, description
FROM movie m
WHERE EXISTS (
    SELECT 1 FROM session s
    WHERE s.movie_id = m.id
      AND s.start_time >= CURRENT_DATE 
      AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
);

-- 2. Подсчёт проданных билетов за неделю
SELECT COUNT(*) AS total_tickets_sold
FROM ticket
WHERE purchased_at >= CURRENT_DATE - INTERVAL '7 days' AND status = 'paid';

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
SELECT 
    m.id,
    m.title,
    m.duration,
    m.description,
    STRING_AGG(TO_CHAR(s.start_time, 'HH24:MI'), ', ' ORDER BY s.start_time) AS sessions
FROM movie m
INNER JOIN session s ON m.id = s.movie_id
WHERE s.start_time >= CURRENT_DATE 
  AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
GROUP BY m.id, m.title, m.duration, m.description
ORDER BY m.title;

-- 4. Поиск 3 самых прибыльных фильмов за неделю
SELECT 
    m.id,
    m.title,
    COUNT(t.id) AS tickets_sold,
    SUM(t.total_price) AS total_revenue
FROM movie m
INNER JOIN session s ON m.id = s.movie_id
INNER JOIN ticket t ON s.id = t.session_id
WHERE t.purchased_at >= CURRENT_DATE - INTERVAL '7 days'
  AND t.status = 'paid'
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
LIMIT 3;

-- 5. Схема зала со свободными и занятыми местами на конкретный сеанс
SELECT 
    se.id AS seat_id,
    se.nrow AS row_number,
    se.seat_number,
    sc.name AS category,
    CASE 
        WHEN t.id IS NOT NULL THEN 'occupied'
        ELSE 'available'
    END AS status
FROM seat se
INNER JOIN hall h ON se.hall_id = h.id
INNER JOIN seat_category sc ON se.seat_category_id = sc.id
INNER JOIN session s ON h.id = s.hall_id
LEFT JOIN ticket t ON se.id = t.seat_id AND t.session_id = s.id
WHERE s.id = 1  -- ID сеанса
ORDER BY se.nrow, se.seat_number;

-- 6. Диапазон минимальной и максимальной цены за билет на конкретный сеанс
SELECT 
    s.id AS session_id,
    m.title AS movie_title,
    s.start_time,
    s.base_price,
    MIN(s.base_price * sc.price_multiplier) AS min_ticket_price,
    MAX(s.base_price * sc.price_multiplier) AS max_ticket_price
FROM session s
INNER JOIN movie m ON s.movie_id = m.id
INNER JOIN hall h ON s.hall_id = h.id
INNER JOIN seat se ON h.id = se.hall_id
INNER JOIN seat_category sc ON se.seat_category_id = sc.id
WHERE s.id = 1  -- ID сеанса (параметр)
GROUP BY s.id, m.title, s.start_time, s.base_price;
