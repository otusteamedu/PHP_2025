
CREATE TABLE Hall (
    hall_id SERIAL PRIMARY KEY,
    capacity INT NOT NULL
);

CREATE TABLE Movie (
    movie_id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    genre VARCHAR(100) NOT NULL
);

CREATE TABLE Session (
    session_id SERIAL PRIMARY KEY,
    hall_id INT,
    movie_id INT,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (hall_id) REFERENCES Hall(hall_id),
    FOREIGN KEY (movie_id) REFERENCES Movie(movie_id)
);


CREATE TABLE SeatTypes (
    seat_type_id SERIAL PRIMARY KEY,
    title VARCHAR(50) NOT NULL
);

CREATE TABLE Seats (
    seat_id SERIAL PRIMARY KEY,
    hall_id INT,
    row_number INT NOT NULL,
    seat_number INT NOT NULL,
    seat_type_id INT,
    FOREIGN KEY (hall_id) REFERENCES Hall(hall_id),
    FOREIGN KEY (seat_type_id) REFERENCES SeatTypes(seat_type_id)
);


CREATE TABLE Client (
    client_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE Ticket (
    ticket_id SERIAL PRIMARY KEY,
    session_id INT,
    client_id INT,
    seat_id INT,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (session_id) REFERENCES Session(session_id),
    FOREIGN KEY (client_id) REFERENCES Client(client_id),
    FOREIGN KEY (seat_id) REFERENCES Seats(seat_id)
);


CREATE TABLE PricingRules (
    pricing_rule_id SERIAL PRIMARY KEY,
    session_id INT, 
    seat_type_id INT,
    modifier DECIMAL(10, 2) NOT NULL, 
    FOREIGN KEY (session_id) REFERENCES Session(session_id),
    FOREIGN KEY (seat_type_id) REFERENCES SeatTypes(seat_type_id)
);
