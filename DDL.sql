USE movie_theathe;

DROP TABLE IF EXISTS `tickets`;

DROP TABLE IF EXISTS `movieSessions`;

DROP TABLE IF EXISTS `movie_sessions`;

DROP TABLE IF EXISTS `movieRooms`;

DROP TABLE IF EXISTS `movie_rooms`;

DROP TABLE IF EXISTS `movieRoomTypes`;

DROP TABLE IF EXISTS `movie_room_types`;

DROP TABLE IF EXISTS `moviesGenres`;

DROP TABLE IF EXISTS `movies_genres`;

DROP TABLE IF EXISTS `movies`;

DROP TABLE IF EXISTS `movieAgeRatings`;

DROP TABLE IF EXISTS `movie_age_ratings`;

DROP TABLE IF EXISTS `genres`;

DROP TABLE IF EXISTS `seats`;

-- MOVIE ROOM TYPES
CREATE TABLE `movie_room_types`(
  `mrt_id` INT AUTO_INCREMENT PRIMARY KEY,
  `mrt_title` VARCHAR(100) NOT NULL,
  `mrt_price` DECIMAL(10, 2) NOT NULL
);

INSERT INTO
  `movie_room_types` (`mrt_title`, `mrt_price`)
VALUES
  ('standard', 300),
  ('premium', 400);

-- MOVIE ROOMS
CREATE TABLE `movie_rooms`(
  `mr_id` INT AUTO_INCREMENT PRIMARY KEY,
  `mr_type` INT NOT NULL,
  FOREIGN KEY (`mr_type`) REFERENCES movie_room_types(`mrt_id`) ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO
  `movie_rooms` (`mr_type`)
VALUES
  (1),
  (1),
  (2);

-- GENRES
CREATE TABLE `genres`(
  `genre_id` INT AUTO_INCREMENT PRIMARY KEY,
  `genre_title` VARCHAR(100) NOT NULL
);

INSERT INTO
  `genres` (`genre_title`)
VALUES
  ('триллер'),
  ('драма'),
  ('романтика'),
  ('комедия'),
  ('боевик'),
  ('фантастика'),
  ('фэнтези'),
  ('ужасы'),
  ('авторское кино');

-- MOVIE AGE RATINGS
CREATE TABLE `movie_age_ratings`(
  `mar_id` INT AUTO_INCREMENT PRIMARY KEY,
  `mar_title` VARCHAR(10) NOT NULL
);

INSERT INTO
  `movie_age_ratings`(`mar_title`)
VALUES
  ('PG-13'),
  ('R'),
  ('PG');

-- MOVIES
CREATE TABLE `movies`(
  `movie_id` INT AUTO_INCREMENT PRIMARY KEY,
  `movie_title` VARCHAR(200) NOT NULL,
  `movie_duration` INT NOT NULL,
  `movie_age_rating` INT NOT NULL,
  `movie_description` TEXT,
  `movie_image` VARCHAR(500),
  FOREIGN KEY (`movie_age_rating`) REFERENCES `movie_age_ratings`(`mar_id`) ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO
  `movies` (
    `movie_title`,
    `movie_duration`,
    `movie_age_rating`,
    `movie_description`
  )
VALUES
  (
    'Звёздные Путешественники',
    120,
    1,
    'В далеком будущем группа смелых исследователей отправляется в захватывающее путешествие через галактики, чтобы найти новый дом для человечества. На их пути они сталкиваются с неизведанными мирами, опасными существами и загадками, которые могут изменить судьбу всей цивилизации.'
  ),
  ('Тени Прошлого', 95, 2, NULL),
  ('Смешные Соседи', 85, 3, NULL),
  ('Ночь Воскрешения', 100, 2, NULL),
  ('Сердца на Льду', 115, 1, NULL),
  ('Гонка на Грани', 130, 2, NULL),
  ('Приключения Лесного Друга', 180, 2, NULL),
  ('Сквозь Века', 125, 1, NULL);

-- MOVIES GENRES
CREATE TABLE `movies_genres`(
  `mg_movie` INT NOT NULL,
  `mg_genre` INT NOT NULL,
  PRIMARY KEY (`mg_movie`, `mg_genre`),
  FOREIGN KEY (`mg_movie`) REFERENCES `movies`(`movie_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`mg_genre`) REFERENCES `genres`(`genre_id`) ON UPDATE CASCADE ON DELETE CASCADE
);

INSERT INTO
  `movies_genres` (`mg_movie`, `mg_genre`)
VALUES
  (1, 6),
  (2, 1),
  (2, 2),
  (3, 4),
  (4, 8),
  (5, 3),
  (6, 5),
  (7, 9),
  (7, 8),
  (8, 7);

-- MOVIE SESSIONS
CREATE TABLE `movie_sessions`(
  `ms_id` INT AUTO_INCREMENT PRIMARY KEY,
  `ms_movie_room` INT NOT NULL,
  `ms_movie` INT NOT NULL,
  `ms_start_time` TIMESTAMP NOT NULL,
  FOREIGN KEY (`ms_movie_room`) REFERENCES `movie_rooms`(`mr_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`ms_movie`) REFERENCES `movies`(`movie_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  UNIQUE (`ms_movie_room`, `ms_movie`, `ms_start_time`)
);

INSERT INTO
  `movie_sessions` (`ms_movie_room`, `ms_movie`, `ms_start_time`)
VALUES
  (1, 1, '2025-05-30 10:00:00'),
  (1, 1, '2025-05-30 12:15:00'),
  (1, 1, '2025-05-30 15:00:00'),
  (1, 3, '2025-05-30 17:45:00'),
  (1, 5, '2025-05-30 19:25:00'),
  (1, 5, '2025-05-30 21:10:00'),
  (1, 6, '2025-05-30 23:35:00'),
  (2, 3, '2025-05-30 10:00:00'),
  (2, 3, '2025-05-30 11:40:00'),
  (2, 1, '2025-05-30 13:20:00'),
  (2, 8, '2025-05-30 16:05:00'),
  (2, 7, '2025-05-30 18:25:00'),
  (2, 4, '2025-05-30 21:40:00'),
  (2, 4, '2025-05-30 23:35:00'),
  (3, 2, '2025-05-30 10:00:00'),
  (3, 2, '2025-05-30 11:50:00'),
  (3, 2, '2025-05-30 13:40:00'),
  (3, 2, '2025-05-30 16:25:00'),
  (3, 2, '2025-05-30 18:50:00'),
  (3, 2, '2025-05-30 22:05:00'),
  (3, 2, '2025-05-30 00:15:00');

-- SEATS
CREATE TABLE `seats`(
  `seat_id` INT AUTO_INCREMENT PRIMARY KEY,
  `seat_row` INT NOT NULL,
  `seat_cell` INT NOT NULL,
  `seat_coefficient` FLOAT NOT NULL,
  UNIQUE (`seat_row`, `seat_cell`)
);

INSERT INTO
  `seats`(`seat_row`, `seat_cell`, `seat_coefficient`)
VALUES
  (1, 1, 0.9),
  (1, 2, 0.9),
  (1, 3, 0.9),
  (1, 4, 1),
  (1, 5, 1),
  (1, 6, 1),
  (1, 7, 0.9),
  (1, 8, 0.9),
  (1, 9, 0.9),
  (2, 1, 0.9),
  (2, 2, 0.9),
  (2, 3, 1),
  (2, 4, 1),
  (2, 5, 1),
  (2, 6, 1),
  (2, 7, 1),
  (2, 8, 0.9),
  (2, 9, 0.9),
  (3, 1, 0.9),
  (3, 2, 0.9),
  (3, 3, 1),
  (3, 4, 1),
  (3, 5, 1),
  (3, 6, 1),
  (3, 7, 1),
  (3, 8, 0.9),
  (3, 9, 0.9),
  (4, 1, 0.9),
  (4, 2, 0.9),
  (4, 3, 1),
  (4, 4, 1),
  (4, 5, 1),
  (4, 6, 1),
  (4, 7, 1),
  (4, 8, 0.9),
  (4, 9, 0.9),
  (5, 1, 0.9),
  (5, 2, 1),
  (5, 3, 1),
  (5, 4, 1),
  (5, 5, 1),
  (5, 6, 1),
  (5, 7, 1),
  (5, 8, 1),
  (5, 9, 0.9),
  (6, 1, 0.9),
  (6, 2, 1),
  (6, 3, 1),
  (6, 4, 1),
  (6, 5, 1),
  (6, 6, 1),
  (6, 7, 1),
  (6, 8, 1),
  (6, 9, 0.9),
  (7, 1, 0.9),
  (7, 2, 1),
  (7, 3, 1),
  (7, 4, 1),
  (7, 5, 1),
  (7, 6, 1),
  (7, 7, 1),
  (7, 8, 1),
  (7, 9, 0.9);

-- USERS
CREATE TABLE `users`(
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_firstname` VARCHAR(100),
  `user_lastname` VARCHAR(100),
  `user_phone` VARCHAR(11) NOT NULL
);

-- ORDERS
CREATE TABLE `orders`(
  `order_id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_user` INT NOT NULL,
  `order_created_time` TIMESTAMP NOT NULL,
  `order_payment_time` TIMESTAMP,
  FOREIGN KEY (`order_user`) REFERENCES `users`(`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
);

-- TICKETS
CREATE TABLE `tickets`(
  `ticket_id` INT AUTO_INCREMENT PRIMARY KEY,
  `ticket_movie_session` INT NOT NULL,
  `ticket_seat` INT NOT NULL,
  `ticket_order` INT NOT NULL,
  FOREIGN KEY (`ticket_movie_session`) REFERENCES `movie_sessions`(`ms_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`ticket_seat`) REFERENCES `seats`(`seat_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`ticket_order`) REFERENCES `orders`(`order_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  UNIQUE (`ticket_movie_session`, `ticket_seat`)
);

-- Код сгенерирован с помощью AI для заполнения билетов.
SET
  @total_seats = 36;

SET
  @total_sessions = 21;

-- Определяем диапазон заполненности
SET
  @min_fill = 0.5;

-- 50%
SET
  @max_fill = 0.95;

-- 95%
-- Генерируем количество билетов для каждого сеанса
INSERT INTO
  tickets (ticket_movie_session, ticket_seat)
SELECT
  ms.ms_id AS ticket_movie_session,
  s.seat_id AS ticket_seat
FROM
  movie_sessions ms
  JOIN seats s ON s.seat_id <= @total_seats
WHERE
  RAND() < (@min_fill + (RAND() * (@max_fill - @min_fill)))
ORDER BY
  ms.ms_id,
  s.seat_id;