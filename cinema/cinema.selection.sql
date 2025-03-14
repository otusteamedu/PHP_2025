USE cinema;

/* Выбор всех фильмов на сегодня */;
/* Фильмы, которые показывают сегодня */;
SELECT films.title,genres.title as film_genre, datetime
FROM screenings
RIGHT JOIN films ON screenings.film_id = films.id
RIGHT JOIN genres ON films.genre_id = genres.id
WHERE datetime BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL '1' DAY)
ORDER BY datetime DESC;

/* Подсчёт проданных билетов за неделю */;
SELECT COUNT(id) AS tickets_purchased_last_week_amount FROM tickets WHERE date_of_purchase > DATE_SUB(CURDATE(), INTERVAL '7' DAY);

/* Поиск 3 самых прибыльных фильмов за неделю */;
SELECT films.title, SUM(tickets.price) AS total_week_film_profit
FROM tickets
LEFT JOIN screenings ON tickets.screening_id = screenings.id
LEFT JOIN films ON screenings.film_id = films.id
WHERE date_of_purchase > DATE_SUB(CURDATE(), INTERVAL '7' DAY)
GROUP BY screening_id
ORDER BY total_week_film_profit DESC
LIMIT 3;