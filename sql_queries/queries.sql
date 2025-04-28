-- Простые запросы в которых задействована одна таблица

-- 1. Сумма проданных билетов за сеанс
SELECT 
    COALESCE(SUM(final_price), 0) AS total_revenue
FROM 
    tickets
WHERE 
    session_id = 86;


-- 2. Количество сеансов за последнюю неделю
SELECT 
    COUNT(*) AS sessions_count
FROM 
    sessions
WHERE 
    session_start >= NOW() - INTERVAL '7 days';


-- 3. Афиша. Фильмы которые показывают сегодня
SELECT DISTINCT 
    film_id
FROM 
    sessions
WHERE 
    session_start::date = CURRENT_DATE;



-- Сложные запросы в которых задействованы связи и агрегатные функции

-- 4. Подсчёт дохода от проданных билетов за неделю
EXPLAIN ANALYZE SELECT
    SUM(t.final_price) AS total_revenue_week
FROM
    tickets t
JOIN
    sessions s ON t.session_id = s.id
WHERE
    s.session_start >= NOW() - INTERVAL '7 days';


-- 5. Поиск 3 самых прибыльных фильмов за неделю
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


-- 6. Свободные и занятые места на конкретный сеанс
SELECT 
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

