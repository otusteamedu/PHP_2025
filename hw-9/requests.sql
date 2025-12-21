-- 1.запрос на получение всех сегодняшних сеансов
SELECT f.name
FROM seance s
    JOIN film f ON s.film_id = f.id
WHERE s.seance_time::date = CURRENT_DATE;

-- 2.запрос на подсчет проданных билетов за неделю
SELECT COUNT(*)
FROM ticket t
    JOIN seance s ON t.seance_id = s.id
WHERE s.seance_time::date BETWEEN CURRENT_DATE - INTERVAL '7 days' AND CURRENT_DATE;

-- 3.запрос на получение фильмов которые показывают сегодня
SELECT DISTINCT f.name
FROM seance s
    JOIN film f ON s.film_id = f.id
WHERE s.seance_time::date = CURRENT_DATE;

-- 4.запрос на получение 3 самых прибыльных фильмов за неделю
SELECT f.name AS film_name,
    SUM(t.price) AS total_income
FROM ticket t
    JOIN seance s ON t.seance_id = s.id
    JOIN film f ON s.film_id = f.id
WHERE t.status = 'sold'
    AND s.seance_time::date BETWEEN CURRENT_DATE - INTERVAL '7 days' AND CURRENT_DATE
GROUP BY f.name
ORDER BY total_income DESC
    LIMIT 3;

-- 5.запрос на генерацию схемы зала и занятых мест
SELECT r AS row,
    string_agg(
           CASE WHEN t.status = 'sold' THEN 'X' ELSE 'O' END,
           '' ORDER BY c
    ) AS seats
FROM seance s
    CROSS JOIN generate_series(1,12) r
    CROSS JOIN generate_series(1,12) c
    LEFT JOIN ticket t
           ON t.seance_id = s.id
               AND t.row = r
               AND t.seat = c
WHERE s.id = 1
GROUP BY r
ORDER BY r;

-- 6.запрос на диапазон минимальной и максимальной цены на конкретный сеанс
SELECT
    MIN(price) AS min_price,
    MAX(price) AS max_price
FROM ticket
WHERE seance_id = 1;
