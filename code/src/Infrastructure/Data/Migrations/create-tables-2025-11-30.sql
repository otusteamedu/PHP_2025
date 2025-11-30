-- Удаление таблиц если существуют (обратный порядок из-за внешних ключей)
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS prices;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS seats;
DROP TABLE IF EXISTS movies;
DROP TABLE IF EXISTS zones;
DROP TABLE IF EXISTS halls;

-- Таблица фильмов
CREATE TABLE movies (
    id SERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    duration_minutes INTEGER NOT NULL CHECK (duration_minutes > 0),
    age_rating INTEGER NOT NULL CHECK (age_rating BETWEEN 0 AND 21),
    start_distribution DATE NOT NULL,
    end_distribution DATE NOT NULL
);

COMMENT ON TABLE movies IS 'Таблица фильмов';
COMMENT ON COLUMN movies.title IS 'Название фильма';
COMMENT ON COLUMN movies.duration_minutes IS 'Продолжительность фильма в минутах';
COMMENT ON COLUMN movies.age_rating IS 'Возрастной рейтинг фильма';
COMMENT ON COLUMN movies.start_distribution IS 'Дата начала проката';
COMMENT ON COLUMN movies.end_distribution IS 'Дата окончания проката';

-- Таблица залов
CREATE TABLE halls (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

COMMENT ON TABLE halls IS 'Таблица кинозалов';
COMMENT ON COLUMN halls.name IS 'Название зала';

-- Таблица зон залов
CREATE TABLE zones (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

COMMENT ON TABLE zones IS 'Таблица зон в кинозалах';
COMMENT ON COLUMN zones.name IS 'Название зоны';

-- Таблица сеансов
CREATE TABLE sessions (
    id SERIAL PRIMARY KEY,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    id_movie INTEGER NOT NULL REFERENCES movies(id) ON DELETE CASCADE,
    id_hall INTEGER NOT NULL REFERENCES halls(id) ON DELETE CASCADE
);

COMMENT ON TABLE sessions IS 'Таблица сеансов';
COMMENT ON COLUMN sessions.start_time IS 'Время начала сеанса';
COMMENT ON COLUMN sessions.end_time IS 'Время окончания сеанса';

-- Таблица мест
CREATE TABLE seats (
    id SERIAL PRIMARY KEY,
    row INTEGER NOT NULL CHECK (row > 0),
    seat INTEGER NOT NULL CHECK (seat > 0),
    id_hall INTEGER NOT NULL REFERENCES halls(id) ON DELETE CASCADE,
    id_zone INTEGER NOT NULL REFERENCES zones(id) ON DELETE CASCADE
);

COMMENT ON TABLE seats IS 'Таблица мест в кинозалах';
COMMENT ON COLUMN seats.row IS 'Номер ряда';
COMMENT ON COLUMN seats.seat IS 'Номер места в ряду';

-- Таблица билетов
CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,
    id_seat INTEGER NOT NULL REFERENCES seats(id) ON DELETE CASCADE,
    id_session INTEGER NOT NULL REFERENCES sessions(id) ON DELETE CASCADE,
    status TEXT NOT NULL CHECK (status IN ('sold', 'booked', 'free')),
    final_price DECIMAL(5,2) NOT NULL CHECK (final_price >= 0), --у клиента может быть скидка, базовые цены могут со временем поменяться, поэтому фиксируем итоговую цену для истории
    update_status_dt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

COMMENT ON TABLE tickets IS 'Таблица билетов';
COMMENT ON COLUMN tickets.status IS 'Статус билета: sold - продан, booked - забронирован, free - свободен';
COMMENT ON COLUMN tickets.final_price IS 'Итоговая цена продажи билета';
COMMENT ON COLUMN tickets.update_status_dt IS 'Дата и время последнего обновления статуса';

-- Таблица цен (составной первичный ключ)
CREATE TABLE prices (
    id_zone INTEGER NOT NULL REFERENCES zones(id) ON DELETE CASCADE,
    id_hall INTEGER NOT NULL REFERENCES halls(id) ON DELETE CASCADE,
    from_time TIME NOT NULL,
    to_time TIME NOT NULL,
    is_weekend BOOLEAN NOT NULL,
    price DECIMAL(5,2) NOT NULL CHECK (price >= 0),
    PRIMARY KEY (id_zone, id_hall, from_time, to_time, is_weekend)
);

COMMENT ON TABLE prices IS 'Таблица цен на билеты по зонам, залам и времени';
COMMENT ON COLUMN prices.from_time IS 'Время начала действия цены (например, 10:00)';
COMMENT ON COLUMN prices.to_time IS 'Время окончания действия цены (например, 14:00)';
COMMENT ON COLUMN prices.is_weekend IS 'Признак выходного дня (true - выходной, false - будний)';
COMMENT ON COLUMN prices.price IS 'Стоимость билета';