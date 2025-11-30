-- Таблица Фильмы
CREATE TABLE Movies (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    title VARCHAR(255) NOT NULL,
    duration_minutes INT NOT NULL,
    genre VARCHAR(100)
);

-- Таблица Залы
CREATE TABLE Halls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    rows_count INT NOT NULL, -- Количество рядов в зале
    seats_per_row INT NOT NULL -- Количество мест в ряду
);

-- Таблица Места
CREATE TABLE Seats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hall_id INT NOT NULL,
    row_number INT NOT NULL,
    seat_number INT NOT NULL,
    seat_type ENUM('standard', 'comfort', 'vip') DEFAULT 'standard',
    FOREIGN KEY (hall_id) REFERENCES Halls(id),
    UNIQUE KEY unique_seat (hall_id, row_number, seat_number) -- Зал-Ряд-Место
);

-- Таблица Сеансы
CREATE TABLE Screenings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    movie_id INT NOT NULL,
    hall_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES Movies(id),
    FOREIGN KEY (hall_id) REFERENCES Halls(id)
);

-- Таблица Билеты
CREATE TABLE Tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    screening_id INT NOT NULL,
    seat_id INT NOT NULL,
    purchase_time DATETIME DEFAULT CURRENT_TIMESTAMP, -- Время покупки
    final_price DECIMAL(10,2) NOT NULL, -- Итоговая цена
    FOREIGN KEY (screening_id) REFERENCES Screenings(id),
    FOREIGN KEY (seat_id) REFERENCES Seats(id),
    UNIQUE KEY unique_ticket (screening_id, seat_id) -- Закрываем продажу одного места дважды
);