CREATE TABLE halls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hall_name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE seats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hall_id INT NOT NULL,
    seat_row INT,
    place INT,
    FOREIGN KEY (hall_id) REFERENCES halls(id),
    UNIQUE (hall_id, seat_row, place)
);

CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    duration int
);

CREATE TABLE schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hall_id INT NOT NULL,
    FOREIGN KEY (hall_id) REFERENCES halls(id),
    time_begin DATETIME NOT NULL,
    movie_id INT NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES movies(id)
);

CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    schedule_id INT NOT NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedule(id),
    price INT,
    seat_id INT NOT NULL,
    FOREIGN KEY (seat_id) REFERENCES seats(id),
    sold BOOLEAN DEFAULT FALSE
);

SELECT 
    m.id,
    m.name AS "Фильм",
    SUM(t.price) AS profit,
    COUNT(t.id) AS tickets_sold
FROM movies m
JOIN schedule s ON m.id = s.movie_id
JOIN tickets t ON s.id = t.schedule_id
WHERE t.sold = TRUE
GROUP BY m.id, m.name
ORDER BY profit DESC
LIMIT 1;