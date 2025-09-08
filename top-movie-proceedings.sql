SELECT movie.movie_name, SUM(booking.booking_price) AS movie_proceedings FROM `booking`
	LEFT JOIN `session` ON `booking`.session_id = `session`.session_id
	LEFT JOIN `movie` ON `session`.movie_id = `movie`.movie_id
	RIGHT JOIN (
		SELECT `order`.order_id as order_id FROM `order`
		LEFT JOIN order_status ON order_status.order_status_id = `order`.order_status_id
		WHERE order_status.status_name = 'Оплачен'
	) as payed_orders ON payed_orders.order_id = booking.order_id
GROUP BY movie.movie_id
ORDER BY movie_proceedings DESC
LIMIT 1