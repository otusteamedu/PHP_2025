-- Создание таблицы кинотеатров
CREATE TABLE cinema (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location TEXT NOT NULL
);

-- Создание таблицы залов
CREATE TABLE hall (
    id SERIAL PRIMARY KEY,
    cinema_id INT REFERENCES cinema(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    capacity INT NOT NULL
);

-- Создание таблицы мест в зале
CREATE TABLE seat (
    id SERIAL PRIMARY KEY,
    hall_id INT REFERENCES hall(id) ON DELETE CASCADE,
    row_number INT NOT NULL,
    seat_number INT NOT NULL,
    UNIQUE (hall_id, row_number, seat_number)
);

-- Создание таблицы фильмов
CREATE TABLE movie (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL -- Длительность в минутах
);

-- Создание таблицы сеансов
CREATE TABLE showtime (
    id SERIAL PRIMARY KEY,
    movie_id INT REFERENCES movie(id) ON DELETE CASCADE,
    hall_id INT REFERENCES hall(id) ON DELETE CASCADE,
    start_time TIMESTAMP NOT NULL
);

-- Создание таблицы категорий цен
CREATE TABLE price_category (
    id SERIAL PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- Создание таблицы билетов
CREATE TABLE ticket (
    id SERIAL PRIMARY KEY,
    showtime_id INT REFERENCES showtime(id) ON DELETE CASCADE,
    seat_id INT REFERENCES seat(id) ON DELETE CASCADE,
    price_category_id INT REFERENCES price_category(id) ON DELETE SET NULL,
    user_id INT, -- Покупатель билета
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (showtime_id, seat_id)
);