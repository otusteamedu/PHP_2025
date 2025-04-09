SELECT
    f.id AS film_id,
    f.title AS film_title,
    SUM(t.final_price) AS total_profit
FROM
    tickets t
JOIN sessions s ON t.session_id = s.id
JOIN films f ON s.film_id = f.id
GROUP BY
    f.id, f.title
ORDER BY
    total_profit DESC
LIMIT 1;