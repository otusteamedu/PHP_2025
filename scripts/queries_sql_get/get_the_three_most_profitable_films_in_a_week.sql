SELECT m.title, SUM(t.price) AS total_revenue
FROM Movie m
         JOIN Session s ON m.id = s.movie_id
         JOIN Ticket t ON s.id = t.session_id
WHERE s.start_time BETWEEN NOW() - INTERVAL '7 days' AND NOW()
GROUP BY m.title
ORDER BY total_revenue DESC
    LIMIT 3;