SELECT m.title
FROM Movie m
         JOIN Session s ON m.id = s.movie_id
WHERE DATE(s.start_time) = CURRENT_DATE;