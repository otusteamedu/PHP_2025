-- Get the most profitable films
SELECT
	film.id AS film_id,
	film.title AS title,
	sum(ticket.price) AS sum

FROM ticket

JOIN session ON ticket.session_id = session.id
JOIN film ON session.film_id = film.id

GROUP BY  film.id

ORDER BY sum DESC 

LIMIT 1;
