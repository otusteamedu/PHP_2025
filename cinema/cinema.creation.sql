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