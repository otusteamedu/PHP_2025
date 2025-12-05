-- создаем таблицу с залами
CREATE TABLE hall (
                      id SERIAL PRIMARY KEY,
                      name VARCHAR(28),
                      seats_count INT
);

-- создаем таблицу с фильмами
CREATE TABLE film (
                      id SERIAL PRIMARY KEY,
                      name VARCHAR(28),
                      year INT,
                      duration INT
);

-- создаем промежуточную таблицу с сеансами
CREATE TABLE seance (
                        id SERIAL PRIMARY KEY,
                        film_id INT NOT NULL REFERENCES film(id) ON DELETE CASCADE,
                        hall_id INT NOT NULL REFERENCES hall(id) ON DELETE CASCADE,
                        price FLOAT NOT NULL,
                        seance_time TIMESTAMP NOT NULL
);

-- создаем таблицу с билетами
CREATE TABLE ticket (
                        id SERIAL PRIMARY KEY,
                        seance_id INT NOT NULL REFERENCES seance(id) ON DELETE CASCADE,
                        row SMALLINT NOT NULL,
                        seat SMALLINT NOT NULL,
                        status VARCHAR(10) NOT NULL DEFAULT 'free',
                        CONSTRAINT unique_seat_per_seance UNIQUE(seance_id, row, seat)
);
