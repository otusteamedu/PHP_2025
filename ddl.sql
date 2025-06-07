CREATE TABLE Cinema (
    cinema_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL
);

CREATE TABLE Hall (
    hall_id INT PRIMARY KEY AUTO_INCREMENT,
    cinema_id INT,
    capacity INT NOT NULL,
    FOREIGN KEY (cinema_id) REFERENCES Cinema(cinema_id)
);

CREATE TABLE Movie (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    genre VARCHAR(100) NOT NULL
);

CREATE TABLE Session (
    session_id INT PRIMARY KEY AUTO_INCREMENT,
    hall_id INT,
    movie_id INT,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    FOREIGN KEY (hall_id) REFERENCES Hall(hall_id),
    FOREIGN KEY (movie_id) REFERENCES Movie(movie_id)
);

CREATE TABLE Client (
    client_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE Ticket (
    ticket_id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT,
    client_id INT,
    seat_number VARCHAR(10) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (session_id) REFERENCES Session(session_id),
    FOREIGN KEY (client_id) REFERENCES Client(client_id)
);