/* Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс */

SELECT 
	films.name film,
    halls.name hall,
  	timetable.time `time`,
	max(price) max_price,
    min(price) min_price
FROM `timetable_seatings`
LEFT JOIN timetable ON timetable.id = timetable_seatings.timetable_id
LEFT JOIN films ON timetable.film_id = films.id
LEFT JOIN halls ON timetable.hall_id = halls.id
WHERE timetable.hall_id = 1 AND timetable.film_id = 1
GROUP BY timetable.time;