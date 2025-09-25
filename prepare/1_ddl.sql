---------------------------------------------------------------------
CREATE TABLE
    room (id SERIAL PRIMARY KEY);
---------------------------------------------------------------------
CREATE TABLE
    room_place (
        id SERIAL PRIMARY KEY,
        id_room INT NOT NULL,
        number_row INT NOT NULL,
        number_place INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (id_room) REFERENCES room (id)
    );
---------------------------------------------------------------------
CREATE TABLE
   film (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL
    );
---------------------------------------------------------------------
CREATE TABLE
    film_session (
        id SERIAL PRIMARY KEY,
        id_room INT NOT NULL,
        id_film INT NOT NULL,
        date_start TIMESTAMP NOT NULL,
        date_end TIMESTAMP NOT NULL,
        FOREIGN KEY (id_room) REFERENCES room (id),
        FOREIGN KEY (id_film) REFERENCES film (id)
    );
---------------------------------------------------------------------
CREATE TABLE
    client (
        id SERIAL PRIMARY KEY,
        date_buy TIMESTAMP NOT NULL
    );
---------------------------------------------------------------------
CREATE TABLE
    ticket (
        id SERIAL PRIMARY KEY,
        id_client INT NOT NULL,
        id_room_place INT NOT NULL,
        id_session INT NOT NULL,
        price DECIMAL(10,2),
        FOREIGN KEY (id_client) REFERENCES client (id),
        FOREIGN KEY (id_room_place) REFERENCES room_place (id),
        FOREIGN KEY (id_session) REFERENCES film_session (id)
    );