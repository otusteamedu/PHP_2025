USE cinema;

/* Получение самого прибыльного фильма */;
SELECT film_id, films.title as film_title, SUM(tickets.price) AS total_film_profit
FROM tickets
LEFT JOIN films
ON tickets.film_id = films.id
GROUP BY film_id
ORDER BY total_film_profit DESC
LIMIT 1;


