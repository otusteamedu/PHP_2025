/*  Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс */

SELECT 

timetable.time `time`,
timetable.hall_id `hall_id`,
timetable.film_id `film_id`,
halls_seats.seat_id seat_id,
timetable_seatings.hall_seat_id hall_seat_id_buy

FROM `timetable`
LEFT JOIN halls_seats ON halls_seats.hall_id = timetable.film_id
LEFT JOIN timetable_seatings ON timetable_seatings.timetable_id = timetable.id AND timetable_seatings.hall_seat_id = halls_seats.id

WHERE timetable.id = 1;