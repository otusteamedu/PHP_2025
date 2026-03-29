-- Таблица фильмов
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(100),
    duration INT NOT NULL, -- Время в минутах
    rating DECIMAL(3,1) CHECK (rating BETWEEN 0 AND 10)
);

-- Таблица залов
CREATE TABLE halls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    type ENUM('Standard', 'IMAX', 'VIP'),
    `rows` INT UNSIGNED NOT NULL,  
    seats_per_row INT UNSIGNED NOT NULL
);

-- Таблица сеансов
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_time TIMESTAMP NOT NULL,
    base_price DECIMAL(10,2) NOT NULL, -- Базовая цена
    movie_id INT,
    hall_id INT,
    CONSTRAINT fk_sessions_movie FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    CONSTRAINT fk_sessions_hall FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE
);

-- Таблица мест 
CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    row_num INT NOT NULL,
    seat_number INT NOT NULL,
    hall_id INT,
    CONSTRAINT fk_seats_hall FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE,
    UNIQUE(hall_id, row_num, seat_number)
);

-- Таблица билетов
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('Reserved', 'Paid'),
    session_id INT,
    seat_id INT,
    CONSTRAINT fk_tickets_session FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    CONSTRAINT fk_tickets_seat FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE,
    UNIQUE(session_id, seat_id)
);

-- Таблица клиентов
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);

-- Таблица продаж
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('Card', 'Cash', 'Online'),
    ticket_id INT,
    customer_id INT,
    CONSTRAINT fk_sales_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);