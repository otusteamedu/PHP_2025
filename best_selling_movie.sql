SELECT
    m.title AS movie_title,
    SUM(t.price) AS total_revenue
FROM
    ticket t
        JOIN screening s ON t.screening_id = s.screening_id
        JOIN movie m ON s.movie_id = m.movie_id
GROUP BY
    m.movie_id, m.title
ORDER BY
    total_revenue DESC
    LIMIT 1;
