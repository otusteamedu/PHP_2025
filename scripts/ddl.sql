CREATE TABLE movies (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    release_date DATE NOT NULL
);

CREATE TABLE attribute_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    data_type VARCHAR(50) NOT NULL CHECK (data_type IN ('TEXT', 'BOOLEAN', 'DATE', 'FLOAT'))
);

CREATE TABLE attributes (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    attribute_type_id INT NOT NULL,
    FOREIGN KEY (attribute_type_id) REFERENCES attribute_types(id)
);

CREATE TABLE values (
    id SERIAL PRIMARY KEY,
    movie_id INT NOT NULL,
    attribute_id INT NOT NULL,
    value_text TEXT,
    value_boolean BOOLEAN,
    value_date DATE,
    value_float FLOAT,
    FOREIGN KEY (movie_id) REFERENCES movies(id),
    FOREIGN KEY (attribute_id) REFERENCES attributes(id)
);
