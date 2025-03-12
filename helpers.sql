-- Запрос для нахождения самого прибыльного фильма
SELECT m.title, SUM(pc.price) AS total_revenue
FROM ticket t
    JOIN showtime s ON t.showtime_id = s.id
    JOIN movie m ON s.movie_id = m.id
    JOIN price_category pc ON t.price_category_id = pc.id
GROUP BY m.title
ORDER BY total_revenue DESC
    LIMIT 1;