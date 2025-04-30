-- Get the most profitable films
SELECT
	film.id AS film_id,
	film.title AS film_title,
	sum(ticket.price) AS sum

FROM ticket

JOIN order ON ticket.order_id = order.id
JOIN payment ON order.id = payment.order_id
JOIN session ON ticket.session_id = session.id
join film ON session.film_id = film.id

WHERE order.status = 'payed' AND payment.is_payd = 'Y'

GROUP BY  film.id

ORDER BY sum DESC 

LIMIT 1;
