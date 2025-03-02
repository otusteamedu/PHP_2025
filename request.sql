-- Get the most profitable films
SELECT
	film.id AS film_id,
	film.title AS title,
	sum(ticket.price) AS sum

FROM ticket

JOIN session ON ticket.session_id = session.id
JOIN film ON session.film_id = film.id
JOIN place ON ticket.ticket_id = place.id
JOIN free_place ON session.id = free_place.session_id 
	AND place.id = free_place.place_id 

WHERE free_place.is_free = 1 

GROUP BY  film.id

ORDER BY sum DESC 

LIMIT 1;
