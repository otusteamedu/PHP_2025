CREATE TABLE movies (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

CREATE TYPE attr_type_enum AS ENUM ('string', 'boolean', 'datetime', 'integer', 'numeric');

CREATE TABLE attr_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    type attr_type_enum NOT NULL,
    service BOOLEAN DEFAULT FALSE
);

CREATE TABLE attributes (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    attr_type_id INTEGER NOT NULL REFERENCES attr_types(id) ON DELETE CASCADE,
    UNIQUE(name, attr_type_id)
);

CREATE TABLE values (
    id SERIAL PRIMARY KEY,
    movie_id INTEGER NOT NULL REFERENCES movies(id) ON DELETE CASCADE,
    attribute_id INTEGER NOT NULL REFERENCES attributes(id) ON DELETE CASCADE,
    string_val TEXT,
    boolean_val BOOLEAN,
    datetime_val TIMESTAMP,
    integer_val INTEGER,
    numeric_val NUMERIC(10, 2)
);

CREATE INDEX idx_values_movie_id ON values(movie_id);
CREATE INDEX idx_values_attribute_id ON values(attribute_id);
CREATE INDEX idx_values_movie_attr ON values(movie_id, attribute_id);

CREATE INDEX idx_attributes_type_id ON attributes(attr_type_id);
CREATE INDEX idx_attr_types_service ON attr_types(service);
CREATE INDEX idx_attr_types_type ON attr_types(type);
