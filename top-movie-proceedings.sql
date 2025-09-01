SELECT movie.movie_id, movie.movie_name, SUM(sum_by_session.session_proceedings) AS movie_total FROM movie
LEFT JOIN `session` ON movie.movie_id = session.movie_id

LEFT JOIN (
	SELECT booking.session_id, SUM(seat_type.price) AS session_proceedings FROM booking
		LEFT JOIN seat ON seat.seat_id = booking.seat_id
		LEFT JOIN seat_type ON seat_type.seat_type_id = seat.seat_type_id
		LEFT JOIN `order` ON `order`.order_id = booking.order_id
	WHERE `order`.`status` = 2
	GROUP BY session_id
) sum_by_session ON sum_by_session.session_id = session.session_id

GROUP BY movie.movie_id
ORDER BY movie_total DESC
LIMIT 1