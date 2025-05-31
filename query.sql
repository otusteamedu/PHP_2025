USE movie_theathe;

-- Вычисление цены билета 
SELECT
  `ms_start_time` as `start_time`,
  `movie_title`,
    (
        `mrt_price` * calculate_time_coefficient(`ms_start_time`) * `seat_coefficient`
    ) AS `price`
FROM
    `movie_sessions`
    JOIN `movie_rooms` ON `movie_rooms`.`mr_id` = `movie_sessions`.`ms_movie_room`
    JOIN `movie_room_types` ON `movie_room_types`.`mrt_id` = `movie_rooms`.`mr_type`
    JOIN `tickets` ON `movie_sessions`.`ms_id` = `tickets`.`ticket_movie_session`
    JOIN `seats` ON `seats`.`seat_id` = `tickets`.`ticket_seat`
    JOIN `movies` ON `movies`.`movie_id` = `movie_sessions`.`ms_movie`;



-- Вычисление самого прибыльного фильма
SELECT
  `movie_title` as `movie`,
    SUM(
        `mrt_price` * calculate_time_coefficient(`ms_start_time`) * `seat_coefficient`
    ) AS `score`
FROM
    `movie_sessions`
    JOIN `movie_rooms` ON `movie_rooms`.`mr_id` = `movie_sessions`.`ms_movie_room`
    JOIN `movie_room_types` ON `movie_room_types`.`mrt_id` = `movie_rooms`.`mr_type`
    JOIN `tickets` ON `movie_sessions`.`ms_id` = `tickets`.`ticket_movie_session`
    JOIN `seats` ON `seats`.`seat_id` = `tickets`.`ticket_seat`
    JOIN `movies` ON `movies`.`movie_id` = `movie_sessions`.`ms_movie`
    GROUP BY `ms_movie`
    ORDER BY `score` DESC
    LIMIT 1;
    