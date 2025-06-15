CREATE TABLE films (
    film_id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    year  INT
);

CREATE TABLE attribute_types (
    type_id SERIAL PRIMARY KEY,
    type_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE attributes (
    attribute_id SERIAL PRIMARY KEY,
    attribute_name VARCHAR(255) NOT NULL,
    type_id INT NOT NULL REFERENCES attribute_types(type_id)
);

CREATE TABLE attribute_values (
    value_id BIGSERIAL PRIMARY KEY,
    film_id INT NOT NULL REFERENCES films(film_id),
    attribute_id INT NOT NULL REFERENCES attributes(attribute_id),
    text_value TEXT,
    date_value DATE,
    boolean_value BOOLEAN,
    integer_value INT,
    decimal_value NUMERIC(10, 2)
);
