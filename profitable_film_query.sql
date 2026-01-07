SELECT
    m.title AS movie_title,
    COUNT(t.id) AS tickets_sold,
    SUM(t.final_price) AS total_revenue
FROM tickets t
         JOIN screenings s ON t.screening_id = s.id
         JOIN movies m ON s.movie_id = m.id
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
    LIMIT 1;
