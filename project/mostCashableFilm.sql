SELECT
    m.movie_id,
    m.title,
    SUM(tp.price) as total_revenue
FROM Movies m
         JOIN Sessions s ON m.movie_id = s.movie_id
         JOIN Tickets t ON s.session_id = t.session_id
         JOIN TicketPrices tp ON t.price_id = tp.price_id
GROUP BY m.movie_id, m.title
ORDER BY total_revenue DESC
    LIMIT 1;