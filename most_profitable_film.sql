SELECT f.name AS film_name,

    (SELECT SUM(t.final_price)
    FROM tickets AS t
    JOIN sessions AS s ON t.session_id = s.id
    WHERE s.film_id = f.id) AS total_revenue

FROM films AS f
ORDER BY total_revenue DESC LIMIT 1;
