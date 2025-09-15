SELECT
    m.id AS movie_id,
    m.name AS movie_name,
    SUM(t.price) AS revenue
FROM cinema.ticket t
JOIN cinema.session s ON t.session_id = s.id
JOIN cinema.movie m ON s.movie_id = m.id
GROUP BY m.id, m.name
ORDER BY revenue DESC
LIMIT 1;
