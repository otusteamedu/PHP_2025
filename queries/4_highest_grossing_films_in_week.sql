select movie.movie_name, SUM(booking.booking_price) as film_proceedings
from
	booking
left join "order" on booking.order_id = "order".order_id
left join "session" on booking.session_id = "session".session_id
left join movie on "session".movie_id = movie.movie_id
where "order".created_at::date >= (now() - interval '7 days') and "order".order_status_id in (2, 4)

group by movie.movie_id
order by film_proceedings desc
limit 3;

