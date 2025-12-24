-- Запросы для создания структуры таблиц и индексов

CREATE TABLE HALL_LAYOUT (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    layout_config JSONB
);

CREATE TABLE HALL (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    capacity INT NOT NULL,
    hall_layout_id INT NOT NULL,
    FOREIGN KEY (hall_layout_id) REFERENCES HALL_LAYOUT(id) ON DELETE RESTRICT
);

CREATE TABLE HALL_SEAT (
    id SERIAL PRIMARY KEY,
    hall_layout_id INT NOT NULL,
    row_number INT NOT NULL,
    seat_number INT NOT NULL,
    seat_type VARCHAR(50) DEFAULT 'Стандарт',
    price_multiplier DECIMAL(4,2) DEFAULT 1.00,
    FOREIGN KEY (hall_layout_id) REFERENCES HALL_LAYOUT(id) ON DELETE RESTRICT
);

CREATE TABLE MOVIE (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL,
    genre VARCHAR(100),
    rating DECIMAL(3,1) -- По 5-бальной шкале
);

CREATE TABLE CUSTOMER (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE
);

CREATE TABLE SESSION (
    id SERIAL PRIMARY KEY,
    hall_id INT NOT NULL,
    movie_id INT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (hall_id) REFERENCES HALL(id) ON DELETE RESTRICT,
    FOREIGN KEY (movie_id) REFERENCES MOVIE(id) ON DELETE RESTRICT,
    CONSTRAINT chk_end_time_after_start_time CHECK (end_time > start_time)
);

CREATE TABLE SEAT_PRICING (
    id SERIAL PRIMARY KEY,
    session_id INT NOT NULL,
    hall_seat_id INT NOT NULL,
    price_multiplier DECIMAL(4,2) NOT NULL,
    comment VARCHAR(255),
    FOREIGN KEY (session_id) REFERENCES SESSION(id) ON DELETE CASCADE,
    FOREIGN KEY (hall_seat_id) REFERENCES HALL_SEAT(id) ON DELETE CASCADE,
    UNIQUE (session_id, hall_seat_id)
);

CREATE TABLE TICKET (
    id SERIAL PRIMARY KEY,
    session_id INT NOT NULL,
    hall_seat_id INT NOT NULL,
    customer_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    purchase_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES SESSION(id) ON DELETE RESTRICT,
    FOREIGN KEY (hall_seat_id) REFERENCES HALL_SEAT(id) ON DELETE RESTRICT,
    FOREIGN KEY (customer_id) REFERENCES CUSTOMER(id) ON DELETE RESTRICT,
    UNIQUE (session_id, hall_seat_id)
);

CREATE INDEX idx_session_start_time ON SESSION(start_time);
CREATE INDEX idx_session_end_time ON SESSION(end_time);
CREATE INDEX idx_session_movie_id ON SESSION(movie_id);
CREATE INDEX idx_session_hall_id ON SESSION(hall_id);
CREATE INDEX idx_ticket_session_id ON TICKET(session_id);
CREATE INDEX idx_ticket_hall_seat_id ON TICKET(hall_seat_id);
CREATE INDEX idx_customer_email ON CUSTOMER(email);
CREATE INDEX idx_hall_seat_layout ON HALL_SEAT(hall_layout_id);
CREATE INDEX idx_hall_layout_id ON HALL(hall_layout_id);

-- Для ускорения генерации
CREATE INDEX idx_ticket_session_seat ON TICKET(session_id, hall_seat_id);
CREATE INDEX  idx_session_time_range ON SESSION(start_time, end_time);
