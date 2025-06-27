-- Нахождение самого прибыльного фильма

SELECT f.title FROM sessions s
LEFT JOIN films f ON s.film_id = f.id
LEFT JOIN seats se ON s.seat_id = se.id
LEFT JOIN seat_category sc ON se.seat_category_id = sc.id
GROUP BY f.title
ORDER BY SUM(sc.cost) DESC
LIMIT 1;