-- Кинотеатры
CREATE TABLE cinema (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Залы
CREATE TABLE hall (
    id SERIAL PRIMARY KEY,
    cinema_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (cinema_id) REFERENCES cinema(id)
);

-- Категории мест (стандарт, премиум, супер премиум и т.п.)
CREATE TABLE seat_category (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price_multiplier DECIMAL(3,2) NOT NULL DEFAULT 1
);

-- Места в зале
CREATE TABLE seat (
    id SERIAL PRIMARY KEY,
    hall_id INT NOT NULL,
    nrow INT NOT NULL,
    seat_number INT NOT NULL,
    seat_category_id INT NOT NULL,
    FOREIGN KEY (hall_id) REFERENCES hall(id),
    FOREIGN KEY (seat_category_id) REFERENCES seat_category(id),
    UNIQUE (hall_id, nrow, seat_number)
);

-- Фильмы
CREATE TABLE movie (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    description TEXT
);

-- Типы атрибутов
CREATE TYPE data_type_enum AS ENUM ('string', 'int', 'numeric', 'float', 'boolean', 'date', 'json');

CREATE TABLE attribute_type (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    data_type data_type_enum NOT NULL
);

CREATE INDEX idx_attr_type_alias ON attribute_type (name);
CREATE INDEX idx_attr_type_data_type ON attribute_type (data_type);

-- Атрибуты
CREATE TABLE attribute (
    id SERIAL PRIMARY KEY,
    attr_type_id INT NOT NULL,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,

    FOREIGN KEY (attr_type_id) REFERENCES attribute_type(id)
);

CREATE INDEX idx_attribute_type ON attribute (attr_type_id);
CREATE INDEX idx_attribute_name ON attribute (name);

-- Значения атрибутов
CREATE TABLE attribute_values (
    id SERIAL PRIMARY KEY,
    movie_id INT NOT NULL,
    attribute_id INT NOT NULL,

    value_string TEXT NULL,
    value_int INT NULL,
    value_numeric DECIMAL(10, 2) NULL,
    value_float DOUBLE PRECISION NULL,
    value_boolean BOOLEAN NULL,
    value_date DATE NULL,
    value_json JSON NULL,

    FOREIGN KEY (movie_id) REFERENCES movie(id),
    FOREIGN KEY (attribute_id) REFERENCES attribute(id)
);   
   
CREATE INDEX idx_values_movie ON attribute_values (movie_id);
CREATE INDEX idx_values_attribute ON attribute_values (attribute_id);

-- Сеансы
CREATE TABLE session (
    id SERIAL PRIMARY KEY,
    movie_id INT NOT NULL,
    hall_id INT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES movie(id),
    FOREIGN KEY (hall_id) REFERENCES hall(id)
);

CREATE INDEX idx_session_start_movie ON session (start_time, movie_id);

-- Статус билета ('reserved', 'paid', 'cancelled', 'used')
CREATE TYPE ticket_status_enum AS ENUM ('reserved', 'paid', 'cancelled', 'used');

-- Билеты
CREATE TABLE ticket (
    id SERIAL PRIMARY KEY,
    session_id INT NOT NULL,
    seat_id INT NOT NULL,
    status ticket_status_enum NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES session(id),
    FOREIGN KEY (seat_id) REFERENCES seat(id),
    UNIQUE (session_id, seat_id)
);

CREATE INDEX idx_ticket_paid_purchased 
ON ticket (purchased_at) 
WHERE status = 'paid';

