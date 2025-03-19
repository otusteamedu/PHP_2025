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
SELECT screening_id, SUM(tickets.price) AS total_week_film_profit
FROM tickets
RIGHT JOIN screenings ON tickets.screening_id = screenings.id
WHERE date_of_purchase > DATE_SUB(CURDATE(), INTERVAL '7' DAY)
GROUP BY screening_id
ORDER BY total_week_film_profit DESC
LIMIT 3;

/* Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс */;
/* Занятые и свободные места будут сформированы скриптом на PHP */;
EXPLAIN SELECT DISTINCT screenings.id as screening_id, screenings.hall_id, tickets.line, tickets.place, hall_line.line_capacity
FROM tickets
JOIN screenings ON tickets.screening_id = screenings.id
JOIN hall_line ON screenings.hall_id = hall_line.hall_id
WHERE tickets.screening_id = 88;

/* Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс */;
SELECT films.title, MIN(tickets.price) AS min_screening_ticket_price, MAX(tickets.price) AS max_screening_ticket_price
FROM tickets
JOIN screenings ON tickets.screening_id = screenings.id
JOIN films ON screenings.film_id = films.id
WHERE tickets.screening_id = 3
GROUP BY screening_id;