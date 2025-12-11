--Фильмы
CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration_min INT NOT NULL CHECK (duration_min > 0),
    rating VARCHAR(10) NOT NULL,
    genre VARCHAR(100),
    director VARCHAR(255),
    actors TEXT,
    release_date DATE,
    poster_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_genre (genre),
    INDEX idx_release_date (release_date)
);

--Зал кинофильма
CREATE TABLE halls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL CHECK (type IN ('2D', '3D', 'IMAX', 'VIP', '4DX')),
    total_seats INT NOT NULL CHECK (total_seats > 0),
    schema JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_hall_name (name)
);

--Места в зале
CREATE TABLE seats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hall_id INT NOT NULL,
    row_num INT NOT NULL CHECK (row_num > 0),
    seat_num INT NOT NULL CHECK (seat_num > 0),
    seat_type VARCHAR(20) NOT NULL DEFAULT 'standard' CHECK (seat_type IN ('standard', 'vip', 'sofa', 'handicap')),
    zone VARCHAR(50) DEFAULT 'main',
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE,
    UNIQUE KEY uk_seat_position (hall_id, row_num, seat_num),
    INDEX idx_hall_id (hall_id),
    INDEX idx_seat_type (seat_type)
);

--Сеансы фильмов
CREATE TABLE screenings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    movie_id INT NOT NULL,
    hall_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'regular' CHECK (type IN ('morning', 'day', 'evening', 'night', 'premier')),
    language VARCHAR(50) DEFAULT 'original',
    subtitles BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE,
    UNIQUE KEY uk_screening_time (hall_id, start_time),
    INDEX idx_movie_id (movie_id),
    INDEX idx_start_time (start_time),
    INDEX idx_type (type),
    CONSTRAINT chk_end_time CHECK (end_time > start_time)
);

--Зрители
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20) UNIQUE,
    birth_date DATE,
    reg_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone)
);

--Правила
CREATE TABLE price_rules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    hall_type VARCHAR(50) NOT NULL,
    seat_type VARCHAR(20) NOT NULL,
    day_of_week INT CHECK (day_of_week BETWEEN 1 AND 7), -- 1=Понедельник, 7=Воскресенье
    time_type VARCHAR(20) NOT NULL, -- 'morning', 'day', 'evening', 'night'
    base_price DECIMAL(10, 2) NOT NULL CHECK (base_price >= 0),
    multiplier DECIMAL(3, 2) DEFAULT 1.00,
    valid_from DATE NOT NULL,
    valid_to DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_price_rule (hall_type, seat_type, day_of_week, time_type, valid_from),
    INDEX idx_hall_type (hall_type),
    INDEX idx_time_type (time_type)
);

--Билеты
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    screening_id INT NOT NULL,
    seat_id INT NOT NULL,
    customer_id INT,
    price DECIMAL(10, 2) NOT NULL CHECK (price >= 0),
    status VARCHAR(20) NOT NULL DEFAULT 'sold' CHECK (status IN ('sold', 'reserved', 'cancelled', 'refunded')),
    purchase_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_method VARCHAR(50) DEFAULT 'cash',
    reservation_expires DATETIME,
    FOREIGN KEY (screening_id) REFERENCES screenings(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    UNIQUE KEY uk_ticket_screening_seat (screening_id, seat_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_purchase_time (purchase_time),
    INDEX idx_screening_id (screening_id)
);

--Скидки
CREATE TABLE discounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    discount_type VARCHAR(20) NOT NULL CHECK (discount_type IN ('percentage', 'fixed', 'special')),
    value DECIMAL(10, 2) NOT NULL,
    min_amount DECIMAL(10, 2),
    valid_from DATE NOT NULL,
    valid_to DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    conditions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_validity (valid_from, valid_to)
);

--Скидки к билетам
CREATE TABLE ticket_discounts (
    ticket_id INT NOT NULL,
    discount_id INT NOT NULL,
    applied_amount DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (ticket_id, discount_id),
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_id) REFERENCES discounts(id) ON DELETE CASCADE
);