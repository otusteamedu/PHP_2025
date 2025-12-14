-- Аналитический запрос
-- Самый прибыльный фильм (по сумме проданных билетов)
-- Цена билета = session.base_price * seat_category.price_multiplier

SELECT 
    m.id,
    m.title,
    COUNT(t.id) AS count_tickets,
    COALESCE(SUM(s.base_price * sc.price_multiplier), 0) AS total_price
FROM movie m
JOIN session s ON s.movie_id = m.id
JOIN ticket t ON t.session_id = s.id
JOIN seat st ON st.id = t.seat_id
JOIN seat_category sc ON sc.id = st.seat_category_id
JOIN ticket_status ts ON ts.id = t.status AND ts.name IN ('paid', 'used')
GROUP BY m.id, m.title
ORDER BY total_price DESC
LIMIT 1;