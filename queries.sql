--  1. Выбор всех фильмов на сегодня
SELECT m.title, m.duration, m.release_year
FROM movies m
LEFT JOIN screenings s ON s.movie_id = m.id
WHERE DATE(s.start_time) = CURRENT_DATE 
GROUP BY m.title, m.duration, m.release_year
ORDER BY m.title;

-- 2. Подсчёт проданных билетов за неделю
SELECT COUNT(DISTINCT t.id) AS tickets_week_count
FROM tickets t
LEFT JOIN screenings s ON s.id = t.screening_id
WHERE DATE(s.start_time) > CURRENT_DATE - INTERVAL '7 days' AND DATE(s.start_time) <= CURRENT_DATE;

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
SELECT m.title, m.duration, m.release_year, to_char(s.start_time, 'HH24:MI') AS start_time, h.name AS hall
FROM movies m
LEFT JOIN screenings s ON s.movie_id = m.id
LEFT JOIN halls h ON h.id = s.hall_id
WHERE DATE(s.start_time) = CURRENT_DATE 
ORDER BY start_time, hall;

-- 4. Поиск 3 самых прибыльных фильмов за неделю
SELECT m.title, SUM(t.price) AS total 
FROM movies m
LEFT JOIN screenings s ON s.movie_id = m.id
LEFT JOIN tickets t ON t.screening_id = s.id
WHERE DATE(s.start_time) > CURRENT_DATE - INTERVAL '7 days' AND DATE(s.start_time) <= CURRENT_DATE
GROUP BY m.title
ORDER BY total DESC
LIMIT 3;

-- 5. Поиск 3 самых прибыльных фильмов за все время
SELECT m.title, SUM(t.price) AS total 
FROM movies m
LEFT JOIN screenings s ON s.movie_id = m.id
LEFT JOIN tickets t ON t.screening_id = s.id
GROUP BY m.title
ORDER BY total DESC
LIMIT 3;

--  6. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
WITH cte AS (
    SELECT se.row_number, se.seat_number, ti.id AS ticket_id 
    FROM screenings sc
    LEFT JOIN halls ha ON ha.id = sc.hall_id
    LEFT JOIN seats se ON se.hall_id = ha.id
    LEFT JOIN tickets ti ON ti.screening_id = sc.id AND ti.seat_id = se.id
    WHERE sc.id = 1
)
SELECT 
	row_number, 
	STRING_AGG((CASE WHEN ticket_id IS NOT NULL THEN '1 ' ELSE '0 ' END)::TEXT, '' ORDER BY seat_number) AS row_seats
FROM cte
GROUP BY row_number
ORDER BY row_number; 

-- 7. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
SELECT MIN(t.price) AS min_price, MAX(t.price) AS max_price
FROM screenings s
LEFT JOIN tickets t ON t.screening_id = s.id
WHERE s.id = 1;

-- 8. Вывести диапазон минимальную и максимальную цену за билет для всех сеансов
SELECT s.id, to_char(s.start_time, 'YYYY-MM-DD HH24:MI:SS') AS start_time, MIN(t.price) AS min_price, MAX(t.price) AS max_price
FROM screenings s
LEFT JOIN tickets t ON t.screening_id = s.id
GROUP by s.id, s.start_time
ORDER BY start_time;

-- 9. Рейтинг пользователей по посещаемости (100 первых)
SELECT c.name, COUNT(t.id) AS ticket_count
FROM customers c
LEFT JOIN tickets t ON t.customer_id = c.id
GROUP BY c.id, c.name 
ORDER BY 2 DESC
LIMIT 100;