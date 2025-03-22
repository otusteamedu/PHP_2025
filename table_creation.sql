CREATE SCHEMA public;

CREATE TABLE film
(
    id    SERIAL PRIMARY KEY NOT NULL,
    title VARCHAR(250)       NOT NULL
);
CREATE INDEX idx_film_title ON film (title);

CREATE TABLE attribute_type
(
    at_id   SERIAL PRIMARY KEY NOT NULL,
    at_type VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE attribute
(
    a_id      SERIAL PRIMARY KEY NOT NULL,
    a_name    VARCHAR(100)       NOT NULL,
    a_type_id INTEGER            NOT NULL REFERENCES attribute_type (at_id)
);

CREATE TABLE value
(
    v_id           SERIAL PRIMARY KEY NOT NULL,
    v_film_id      INTEGER            NOT NULL REFERENCES film (id),
    v_attribute_id INTEGER            NOT NULL REFERENCES attribute (a_id),
    v_date         DATE                        DEFAULT NULL,
    v_date_time    TIMESTAMP WITHOUT TIME ZONE DEFAULT NULL,
    v_varchar      VARCHAR                     DEFAULT NULL,
    v_numeric      NUMERIC(10, 2)              DEFAULT NULL,
    v_float        DOUBLE PRECISION            DEFAULT NULL,
    v_int          INT                         DEFAULT NULL,
    v_bool         BOOLEAN                     DEFAULT NULL,
    v_small_int    SMALLINT                    DEFAULT NULL
);