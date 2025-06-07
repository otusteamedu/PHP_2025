SELECT 
    m.title,
    SUM(t.price) AS total_revenue
FROM 
    Ticket t
JOIN 
    Session s ON t.session_id = s.session_id
JOIN 
    Movie m ON s.movie_id = m.movie_id
GROUP BY 
    m.movie_id, m.title
ORDER BY 
    total_revenue DESC
LIMIT 1;
