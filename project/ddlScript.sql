-- Таблица Movies
CREATE TABLE Movies (
    movie_id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    CONSTRAINT chk_duration CHECK (duration > 0)
);

-- Таблица Halls
CREATE TABLE Halls (
    hall_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    CONSTRAINT uq_hall_name UNIQUE (name)
);

-- Таблица Seats
CREATE TABLE Seats (
   seat_id SERIAL PRIMARY KEY,
   hall_id INT NOT NULL,
   row_number INT NOT NULL,
   seat_number INT NOT NULL,
   seat_type VARCHAR(20) NOT NULL CHECK (seat_type IN ('standard', 'vip')),
   CONSTRAINT fk_seat_hall FOREIGN KEY (hall_id) REFERENCES Halls(hall_id),
   CONSTRAINT uq_hall_row_seat UNIQUE (hall_id, row_number, seat_number),
   CONSTRAINT chk_row_number CHECK (row_number > 0),
   CONSTRAINT chk_seat_number CHECK (seat_number > 0)
);

-- Таблица Sessions
CREATE TABLE Sessions (
    session_id SERIAL PRIMARY KEY,
    movie_id INT NOT NULL,
    hall_id INT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    CONSTRAINT fk_session_movie FOREIGN KEY (movie_id) REFERENCES Movies(movie_id),
    CONSTRAINT fk_session_hall FOREIGN KEY (hall_id) REFERENCES Halls(hall_id),
    CONSTRAINT uq_hall_start_time UNIQUE (hall_id, start_time)
);

-- Таблица TicketPrices
CREATE TABLE TicketPrices (
    price_id SERIAL PRIMARY KEY,
    session_id INT NOT NULL,
    seat_type VARCHAR(20) NOT NULL CHECK (seat_type IN ('standard', 'vip')),
    price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_price_session FOREIGN KEY (session_id) REFERENCES Sessions(session_id),
    CONSTRAINT uq_session_seat_type UNIQUE (session_id, seat_type),
    CONSTRAINT chk_price CHECK (price >= 0)
);

-- Таблица Tickets
CREATE TABLE Tickets (
    ticket_id SERIAL PRIMARY KEY,
    session_id INT NOT NULL,
    seat_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    purchase_time TIMESTAMP NOT NULL,
    CONSTRAINT fk_ticket_session FOREIGN KEY (session_id) REFERENCES Sessions(session_id),
    CONSTRAINT fk_ticket_seat FOREIGN KEY (seat_id) REFERENCES Seats(seat_id),
    CONSTRAINT uq_session_seat UNIQUE (session_id, seat_id)
);