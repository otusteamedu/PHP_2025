SELECT m.title, SUM(t.price) AS total 
FROM movies m
LEFT JOIN screenings s ON s.movie_id = m.id
LEFT JOIN tickets t ON t.screening_id = s.id
GROUP BY m.id
ORDER BY total DESC
LIMIT 1;