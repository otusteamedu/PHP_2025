SELECT m.TITLE, SUM(t.PRICE) AS total
FROM movies m
         LEFT JOIN screenings s ON s.MOVIE_ID = m.ID
         LEFT JOIN tickets t ON t.SCREENING_ID = s.ID
GROUP BY m.ID
ORDER BY total DESC
LIMIT 1;