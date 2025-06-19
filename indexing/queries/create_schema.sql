-- ddl схема из hw6

-- справочник типов мест
CREATE TABLE seat_type (
    seat_type_id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price_modifier DECIMAL(3,2) NOT NULL
);

-- кинотеатры
CREATE TABLE cinema (
    cinema_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255)
);

-- залы
CREATE TABLE hall (
    hall_id SERIAL PRIMARY KEY,
    cinema_id INT NOT NULL REFERENCES cinema(cinema_id),
    name VARCHAR(50),
    rows_count INT NOT NULL CHECK (rows_count > 0),
    seats_per_row INT NOT NULL CHECK (seats_per_row > 0)
);

-- места в залах
CREATE TABLE seat (
    seat_id SERIAL PRIMARY KEY,
    hall_id INT NOT NULL REFERENCES hall(hall_id),
    row_number INT NOT NULL CHECK (row_number > 0),
    seat_number INT NOT NULL CHECK (seat_number > 0),
    seat_type_id INT NOT NULL REFERENCES seat_type(seat_type_id),
    UNIQUE (hall_id, row_number, seat_number)
);

-- фильмы
CREATE TABLE movie (
    movie_id SERIAL PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    duration INT NOT NULL CHECK (duration > 0),
    age_rating VARCHAR(10)
);

-- сеансы (показы)
CREATE TABLE screening (
    screening_id SERIAL PRIMARY KEY,
    movie_id INT NOT NULL REFERENCES movie(movie_id),
    hall_id INT NOT NULL REFERENCES hall(hall_id),
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    base_price DECIMAL(8,2) NOT NULL CHECK (base_price > 0),
    UNIQUE (hall_id, show_date, show_time)
);

-- клиенты
CREATE TABLE customer (
    customer_id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20)
);

-- билеты
CREATE TABLE ticket (
    ticket_id SERIAL PRIMARY KEY,
    screening_id INT NOT NULL REFERENCES screening(screening_id),
    seat_id INT NOT NULL REFERENCES seat(seat_id),
    customer_id INT NOT NULL REFERENCES customer(customer_id),
    price DECIMAL(8,2) NOT NULL CHECK (price > 0),
    purchase_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (screening_id, seat_id)
);
