-- Самый прибыльный фильм за все время
SELECT 
    m.id,
    m.title,
    COUNT(t.id) AS tickets_sold,
    SUM(t.price) AS total_revenue,
    ROUND(AVG(t.price), 2) AS avg_ticket_price,
    COUNT(DISTINCT s.id) AS screenings_count
FROM movies m
JOIN screenings s ON m.id = s.movie_id
JOIN tickets t ON s.id = t.screening_id
WHERE t.status = 'sold'
    AND t.purchase_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) -- можно убрать для всех времен
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
LIMIT 1;

--Самый прибыльный фильм за последний месяц с детализацией
SELECT 
    m.title AS movie_title,
    m.genre,
    m.rating,
    DATE(s.start_time) AS screening_date,
    DAYNAME(s.start_time) AS day_of_week,
    COUNT(t.id) AS tickets_sold,
    SUM(t.price) AS daily_revenue,
    h.name AS hall_name,
    h.type AS hall_type
FROM movies m
JOIN screenings s ON m.id = s.movie_id
JOIN tickets t ON s.id = t.screening_id
JOIN halls h ON s.hall_id = h.id
WHERE t.status = 'sold'
    AND t.purchase_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
GROUP BY m.id, DATE(s.start_time), h.id, m.title, m.genre, m.rating, h.name, h.type
ORDER BY daily_revenue DESC
LIMIT 10;