SELECT 
	sum(tikets.price) film_real_profit, 
	sum(timetable_seatings.price) film_potential_profit, 
	films.name film_name
FROM `tikets` 
LEFT JOIN `timetable_seatings` ON timetable_seatings.id = tikets.timetable_seatings_id
LEFT JOIN `timetable` ON timetable.id = timetable_seatings.timetable_id
LEFT JOIN `films` ON timetable.film_id = films.id
GROUP BY films.id
ORDER BY film_real_profit DESC LIMIT 1;