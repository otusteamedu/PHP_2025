/*  Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс */

SELECT 

	timetable.time `time`,
	timetable.hall_id `hall_id`,
	timetable.film_id `film_id`,
	halls_seats.seat_id seat_id

FROM `timetable`
LEFT JOIN halls_seats ON halls_seats.hall_id = timetable.film_id
WHERE timetable.id = 1;