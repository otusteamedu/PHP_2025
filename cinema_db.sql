-- Справочник категорий мест (Стандарт, VIP, Диван)
CREATE TABLE seat_categories
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Таблица фильмов
CREATE TABLE movies
(
    id               SERIAL PRIMARY KEY,
    title            VARCHAR(255) NOT NULL,
    duration_minutes INT          NOT NULL CHECK (duration_minutes > 0),
    description      TEXT,
    release_year     INT
);

CREATE TABLE halls
(
    id        SERIAL PRIMARY KEY,
    name      VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Таблица мест
CREATE TABLE seats
(
    id               SERIAL PRIMARY KEY,
    hall_id          INT NOT NULL REFERENCES halls (id) ON DELETE CASCADE,
    seat_category_id INT NOT NULL REFERENCES seat_categories (id),
    row_number       INT NOT NULL,
    place_number     INT NOT NULL,
    UNIQUE (hall_id, row_number, place_number)
);

-- Таблица сеансов
CREATE TABLE screenings
(
    id         SERIAL PRIMARY KEY,
    movie_id   INT       NOT NULL REFERENCES movies (id),
    hall_id    INT       NOT NULL REFERENCES halls (id),
    start_time TIMESTAMP NOT NULL
);

-- Таблица цен на сеанс
-- Для каждого сеанса задаем цену для каждой категории мест
CREATE TABLE screening_prices
(
    id               SERIAL PRIMARY KEY,
    screening_id     INT            NOT NULL REFERENCES screenings (id) ON DELETE CASCADE,
    seat_category_id INT            NOT NULL REFERENCES seat_categories (id),
    price            DECIMAL(10, 2) NOT NULL CHECK (price >= 0),
    UNIQUE (screening_id, seat_category_id)
);

-- Таблица билетов
CREATE TABLE tickets
(
    id             SERIAL PRIMARY KEY,
    screening_id   INT            NOT NULL REFERENCES screenings (id),
    seat_id        INT            NOT NULL REFERENCES seats (id),
    customer_email VARCHAR(150),
    sold_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    final_price    DECIMAL(10, 2) NOT NULL,
    UNIQUE (screening_id, seat_id)
);
