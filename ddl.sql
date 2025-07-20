    -- Создание таблицы залов
CREATE TABLE hall (
   id SERIAL PRIMARY KEY,
   num INTEGER NOT NULL
);

-- Создание таблицы типов мест
CREATE TABLE type_place (
    id SERIAL PRIMARY KEY,
    name VARCHAR NOT NULL
);

-- Создание таблицы фильмов
CREATE TABLE movie (
   id SERIAL PRIMARY KEY,
   name VARCHAR NOT NULL,
   release_date DATE NOT NULL,
   duration INTERVAL NOT NULL,  -- Длительность фильма
   description TEXT
);

-- Создание таблицы с описанной схемой зала
CREATE TABLE schema_room (
    id SERIAL PRIMARY KEY,
    row INTEGER NOT NULL, --ряд
    place INTEGER NOT NULL, --место
    type_id INTEGER NOT NULL REFERENCES type_place(id)
    room_id INTEGER NOT NULL REFERENCES room(id)
);

-- Создание таблицы помещений, где проходят сеансы
CREATE TABLE room (
  id SERIAL PRIMARY KEY,
  num INTEGER NOT NULL,
  hall_id INTEGER NOT NULL REFERENCES hall(id),
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
    actual_price DECIMAL(10,2) NOT NULL,
    schema_id INTEGER NOT NULL REFERENCES schema_room(id),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE "type_attribute" (
      "id" integer PRIMARY KEY,
      "name" varchar NOT NULL,
      "data_type" VARCHAR NOT NULL CHECK (status IN ('float', 'int', 'string', 'date', 'bool'))
);

CREATE TABLE "attribute" (
     "id" integer PRIMARY KEY,
     "name" varchar NOT NULL,
     "id_type_attribute" int NOT NULL REFERENCES type_attribute(id)
);

CREATE TABLE "movie_props_value" (
     "id" integer PRIMARY KEY,
     "attribute_id" integer NOT NULL REFERENCES attribute(id),
     "movie_id" integer NOT NULL REFERENCES movie(id),
     "value_string" text,
     "value_float" double precision,
     "value_integer" int,
     "value_date" date,
     "value_boolean" boolean,
);

CREATE INDEX idx_type_attribute ON "attribute" (id_type_attribute);
CREATE INDEX idx_movie_props_value_attribute ON "movie_props_value" (attribute_id);
CREATE INDEX idx_movie_props_value_movie ON "movie_props_value" (movie_id);
