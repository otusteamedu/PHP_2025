SELECT
    m.title AS title,
    m.description AS description,
    m.genre AS genre,
    SUM(t.price) AS total_sum,
    COUNT(t.id) AS tickets_count
FROM MOVIE m
JOIN SESSION s ON m.id = s.movie_id
JOIN TICKET t ON s.id = t.session_id
GROUP BY m.id, m.title, m.description, m.genre
ORDER BY total_sum DESC
LIMIT 1;
