SELECT 
	sum(timetable.price) film_profit, 
	films.name film_name
FROM `tikets` 
LEFT JOIN `timetable` ON timetable.id = tikets.timetable_id 
LEFT JOIN `films` ON timetable.film_id = films.id
GROUP BY films.id
ORDER BY film_profit DESC LIMIT 1