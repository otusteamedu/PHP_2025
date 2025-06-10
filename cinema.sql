create database cinema;

use cinema;

CREATE TABLE halls
(
    id   INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE   NOT NULL,
    type ENUM ('small', 'big') NOT NULL
);

INSERT INTO halls (name, type)
VALUES ('Small Hall', 'small'),
       ('Big Hall', 'big');

create table small_hall
(
    place   INT,
    line    INT,
    type    VARCHAR(255),
    price   DECIMAL(10, 2),
    hall_id INT,
    PRIMARY KEY (place, line),
    FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE
);

INSERT INTO small_hall (place, line, type, price, hall_id)
VALUES (1, 1, 'Standard', 10.00, 1),
       (2, 1, 'Standard', 10.00, 1),
       (3, 1, 'VIP', 15.00, 1),
       (1, 2, 'Standard', 10.00, 1),
       (2, 2, 'VIP', 15.00, 1);

create table big_hall
(
    place   INT,
    line    INT,
    type    VARCHAR(255),
    price   DECIMAL(10, 2),
    hall_id INT,
    PRIMARY KEY (place, line),
    FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE
);

INSERT INTO big_hall (place, line, type, price, hall_id)
VALUES (1, 1, 'Standard', 12.00, 2),
       (2, 1, 'Standard', 12.00, 2),
       (3, 1, 'VIP', 18.00, 2),
       (1, 2, 'Standard', 12.00, 2),
       (2, 2, 'VIP', 18.00, 2);

create table films
(
    id            INT PRIMARY KEY AUTO_INCREMENT,
    name          VARCHAR(255),
    description   TEXT,
    genre         VARCHAR(255),
    continue_time TIME
);

INSERT INTO films (name, description, genre, continue_time)
VALUES ('Film A', 'Description of Film A', 'Action', '02:00:00'),
       ('Film B', 'Description of Film B', 'Comedy', '01:45:00'),
       ('Film C', 'Description of Film C', 'Drama', '02:30:00'),
       ('Film D', 'Description of Film D', 'Horror', '01:50:00'),
       ('Film E', 'Description of Film E', 'Sci-Fi', '02:15:00');

create table timetable
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    hall_id        INT,
    film_id        INT,
    date           DATE,
    beginning_time TIME,
    finish_time    TIME,
    FOREIGN KEY (film_id) REFERENCES films (id) ON DELETE CASCADE,
    FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE
);

INSERT INTO timetable (hall_id, film_id, date, beginning_time, finish_time)
VALUES (1, 1, '2025-10-10', '14:00:00', '16:00:00'),
       (1, 2, '2025-10-10', '17:00:00', '18:45:00'),
       (2, 3, '2025-10-10', '15:00:00', '17:30:00'),
       (2, 4, '2025-10-10', '19:00:00', '20:50:00'),
       (2, 5, '2025-10-10', '21:00:00', '23:15:00');

create table orders
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    hall_id    INT,
    place      INT,
    line       INT,
    film_id    INT,
    price      DECIMAL(10, 2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (film_id) REFERENCES films (id) ON DELETE CASCADE,
    FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE
);

INSERT INTO orders (hall_id, place, line, film_id, price)
VALUES (1, 1, 1, 1, 10.00),
       (1, 2, 1, 2, 10.00),
       (2, 1, 1, 3, 12.00),
       (2, 1, 1, 4, 12.00),
       (2, 1, 1, 5, 18.00);


//Нахождения самого прибыльного фильма
SELECT f.id,
       f.name,
       SUM(o.price) AS sum
FROM films f
    JOIN
    orders o ON f.id = o.film_id
GROUP BY f.id, f.name
ORDER BY sum DESC
    LIMIT 1;
