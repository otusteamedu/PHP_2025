-- Создание таблицы залов
CREATE TABLE hall (
   id SERIAL PRIMARY KEY,
   num INTEGER NOT NULL
);

-- Создание таблицы типов мест
CREATE TABLE type_place (
    id SERIAL PRIMARY KEY,
    name VARCHAR NOT NULL CHECK (status IN ('STANDARD', 'VIP', 'PREMIUM')
);

-- Создание таблицы фильмов
CREATE TABLE movie (
   id SERIAL PRIMARY KEY,
   name VARCHAR NOT NULL,
   release_date DATE NOT NULL,
   duration INTERVAL NOT NULL  -- Длительность фильма
   description TEXT
);

-- Создание таблицы с описанной схемой зала
CREATE TABLE schema_room (
    id SERIAL PRIMARY KEY,
    row INTEGER NOT NULL, --ряд
    place INTEGER NOT NULL, --место
    type_id INTEGER NOT NULL REFERENCES type_place(id)
);

-- Создание таблицы помещений, где проходят сеансы
CREATE TABLE room (
  id SERIAL PRIMARY KEY,
  num INTEGER NOT NULL,
  hall_id INTEGER NOT NULL REFERENCES hall(id),
  schema_id INTEGER NOT NULL REFERENCES schema_room(id),
  effect_type VARCHAR NOT NULL
);


-- Создание таблицы сеансов
CREATE TABLE session_movie (
   id SERIAL PRIMARY KEY,
   room_id INTEGER NOT NULL REFERENCES room(id),
   time_start TIMESTAMP NOT NULL,
   movie_id INTEGER NOT NULL REFERENCES movie(id)
);

-- Создание таблицы цен
CREATE TABLE price_list (
    id SERIAL PRIMARY KEY,
    type_id INTEGER NOT NULL REFERENCES type_place(id),
    price DECIMAL(10,2) NOT NULL,
    session_movie_id INTEGER NOT NULL REFERENCES session_movie(id)
);

-- Создание таблицы билетов
CREATE TABLE ticket (
    id SERIAL PRIMARY KEY,
    session_movie_id INTEGER REFERENCES session_movie(id),
    status VARCHAR NOT NULL CHECK (status IN ('куплен', 'забронирован', 'возвращен', 'использован')),
    schema_id INTEGER NOT NULL REFERENCES schema_room(id),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);