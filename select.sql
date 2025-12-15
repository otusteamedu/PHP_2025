-- Аналитический запрос
-- Самый прибыльный фильм (по сумме проданных билетов) ticket.total_price

SELECT 
    m.id,
    m.title,
    COUNT(t.id) AS count_tickets,
    SUM(t.total_price) AS total_sum
FROM movie m
JOIN session s ON s.movie_id = m.id
JOIN ticket t ON t.session_id = s.id
JOIN ticket_status ts ON ts.id = t.status AND ts.name IN ('paid', 'used')
GROUP BY m.id, m.title
ORDER BY total_sum DESC
LIMIT 1;