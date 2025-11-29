-- Поиск самого прибыльного фильма
SELECT
    films.title AS film_title,
    SUM(sessions.price) AS total
FROM
    films
JOIN
    sessions ON films.id = sessions.film
JOIN
    tickets ON sessions.id = tickets.session_id
GROUP BY
    films.id
ORDER BY
    total DESC;
