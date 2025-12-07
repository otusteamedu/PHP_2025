-- SQL для нахождения самого прибыльного фильма
SELECT m.title, SUM(o.price) as total_price
FROM movie m
         JOIN showtime s ON m.id = s.movie_id
         JOIN "order" o ON s.id = o.showtime_id
GROUP BY m.id, m.title
ORDER BY total_price DESC LIMIT 1;