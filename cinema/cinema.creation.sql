CREATE DATABASE IF NOT EXISTS cinema;
USE cinema;

CREATE TABLE IF NOT EXISTS halls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS hall_line (
   id INT PRIMARY KEY AUTO_INCREMENT,
   hall_id INT,
   line_number TINYINT UNSIGNED NOT NULL,
   line_capacity TINYINT UNSIGNED NOT NULL,
   FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS genres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS films (
   id INT PRIMARY KEY AUTO_INCREMENT,
   title VARCHAR(255) NOT NULL,
   genre_id INT,
   FOREIGN KEY (genre_id) REFERENCES genres (id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS screenings (
   id INT PRIMARY KEY AUTO_INCREMENT,
   hall_id INT,
   film_id INT,
   base_price FLOAT,
   datetime DATETIME,
   FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE,
   FOREIGN KEY (film_id) REFERENCES films (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS hall_price_modificators (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hall_line_id INT,
    place TINYINT UNSIGNED,
    price_modificator FLOAT,
    FOREIGN KEY (hall_line_id) REFERENCES hall_line (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS clients (
  id INT PRIMARY KEY AUTO_INCREMENT,
  fio VARCHAR(255) NOT NULL,
  phone VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tickets (
  id INT PRIMARY KEY AUTO_INCREMENT,
  screening_id INT,
  client_id INT,
  line TINYINT UNSIGNED,
  place TINYINT UNSIGNED,
  price FLOAT,
  date_of_purchase DATETIME,
  FOREIGN KEY (screening_id) REFERENCES screenings (id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE
);

INSERT INTO halls (code, title) VALUES
('action_movies_hall', 'Зал боевиков'),
('love_movies_hall', 'Зал мелодрам'),
('comedy_movies_hall', 'Зал комедий');

INSERT INTO hall_line(hall_id, line_number, line_capacity) VALUES
((SELECT halls.id FROM halls WHERE id = 1 LIMIT 1), 1, 10),
((SELECT halls.id FROM halls WHERE id = 1 LIMIT 1), 2, 10),
((SELECT halls.id FROM halls WHERE id = 1 LIMIT 1), 3, 10);

INSERT INTO genres (code, title) VALUES ('action', 'Боевики'), ('love', 'Мелодрамы'), ('comedy', 'Комедии');



-- INSERT INTO tickets(screening_id, client_id, line,place,price,date_of_purchase) VALUES
-- (
--  (SELECT screenings.id FROM screenings ORDER BY RAND() LIMIT 1),
--  (SELECT clients.id FROM clients ORDER BY RAND() LIMIT 1),
--  RAND()*(3-1)+1,
--  RAND()*(10-1)+1,
--  RAND()*(20-10)+10,
--  NOW()
-- );


-- INSERT INTO clients (fio, phone) VALUES
-- ('Сидоренко Петр Михайлович', '+375333332211'),
-- ('Иванова Татьяна Александровна', '+375293332211'),
-- ('Прокопенков Виктор Павлович', '+375334442241');


-- INSERT INTO films(title, genre_id) VALUES ('Тестовый фильм', (SELECT genres.id FROM genres ORDER BY RAND() LIMIT 1));
--

-- INSERT INTO screenings(hall_id, film_id, base_price, datetime) VALUES
-- ((SELECT halls.id FROM halls ORDER BY RAND() LIMIT 1),(SELECT films.id FROM films ORDER BY RAND() LIMIT 1),RAND()*(20-10)+10,NOW()),
-- ((SELECT halls.id FROM halls ORDER BY RAND() LIMIT 1),(SELECT films.id FROM films ORDER BY RAND() LIMIT 1),RAND()*(20-10)+10,NOW()),
-- ((SELECT halls.id FROM halls ORDER BY RAND() LIMIT 1),(SELECT films.id FROM films ORDER BY RAND() LIMIT 1),RAND()*(20-10)+10,NOW()),
-- ((SELECT halls.id FROM halls ORDER BY RAND() LIMIT 1),(SELECT films.id FROM films ORDER BY RAND() LIMIT 1),RAND()*(20-10)+10,NOW());

