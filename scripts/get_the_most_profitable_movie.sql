SELECT 
    m.title AS movie_title,
    SUM(t.price) AS total_revenue,
    SUM(t.price) / MIN(m.duration) AS revenue_per_hour
FROM 
    Movie m
JOIN 
    Session s ON m.id = s.movie_id
JOIN 
    Ticket t ON s.id = t.session_id
GROUP BY 
    m.title
ORDER BY 
    revenue_per_hour DESC
LIMIT 1;
