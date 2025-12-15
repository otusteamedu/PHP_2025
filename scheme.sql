-- Кинотеатры
CREATE TABLE cinema (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);

-- Залы
CREATE TABLE hall (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cinema_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (cinema_id) REFERENCES cinema(id)
);

-- Категории мест (стандарт, премиум, супер премиум и т.п.)
CREATE TABLE seat_category (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    price_multiplier DECIMAL(3,2) NOT NULL DEFAULT 1
);

-- Места в зале
CREATE TABLE seat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hall_id INT NOT NULL,
    nrow INT NOT NULL,
    seat_number INT NOT NULL,
    seat_category_id INT NOT NULL,
    FOREIGN KEY (hall_id) REFERENCES hall(id),
    FOREIGN KEY (seat_category_id) REFERENCES seat_category(id),
    UNIQUE KEY unique_seat (hall_id, nrow, seat_number)
);

-- Фильмы
CREATE TABLE movie (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    description TEXT
);

-- Сеансы
CREATE TABLE session (
    id INT PRIMARY KEY AUTO_INCREMENT,
    movie_id INT NOT NULL,
    hall_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES movie(id),
    FOREIGN KEY (hall_id) REFERENCES hall(id)
);

-- Статус билета ('reserved', 'paid', 'cancelled', 'used')
CREATE TABLE ticket_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL,
    name VARCHAR(50) NOT NULL
);

-- Билеты
CREATE TABLE ticket (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    seat_id INT NOT NULL,
    status INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES session(id),
    FOREIGN KEY (seat_id) REFERENCES seat(id),
    FOREIGN KEY (status) REFERENCES ticket_status(id),
    UNIQUE KEY unique_ticket (session_id, seat_id)
);
