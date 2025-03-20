CREATE DATABASE cinema;

CREATE TABLE halls (
  id serial primary key,
  code VARCHAR(255) UNIQUE NOT NULL,
  title VARCHAR(255) NOT NULL
);

CREATE TABLE hall_line (
  id serial primary key,
  hall_id INT,
  line_number smallint NOT NULL,
  line_capacity smallint NOT NULL,
  FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE
);

CREATE TABLE genres (
    id serial primary key,
    code VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE films (
   id serial primary key,
   title VARCHAR(255) NOT NULL,
   genre_id INT,
   FOREIGN KEY (genre_id) REFERENCES genres (id) ON DELETE SET NULL
);

CREATE TABLE screenings (
   id serial primary key,
   hall_id INT,
   film_id INT,
   base_price FLOAT,
   datetime TIMESTAMP,
   FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE,
   FOREIGN KEY (film_id) REFERENCES films (id) ON DELETE CASCADE
);

CREATE TABLE hall_price_modificators (
    id serial primary key,
    hall_line_id INT,
    place smallint,
    price_modificator FLOAT,
    FOREIGN KEY (hall_line_id) REFERENCES hall_line (id) ON DELETE CASCADE
);

CREATE TABLE clients (
  id serial primary key,
  fio VARCHAR(255) NOT NULL,
  phone VARCHAR(255) NOT NULL
);

CREATE TABLE tickets (
  id serial primary key,
  screening_id INT,
  client_id INT,
  line smallint,
  place smallint,
  price FLOAT,
  date_of_purchase TIMESTAMP,
  FOREIGN KEY (screening_id) REFERENCES screenings (id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE
);