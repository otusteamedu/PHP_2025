SELECT
    m.movie_id,
    m.movie_name,
    SUM(t.price) AS total_income
FROM app.Tikets t
         JOIN app.Sessions s ON t.session_id = s.session_id
         JOIN app.Movies m ON s.movie_id = m.movie_id
GROUP BY m.movie_id, m.movie_name
ORDER BY total_income DESC
    LIMIT 1;