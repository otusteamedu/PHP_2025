CREATE SCHEMA "CINEMA"

CREATE TABLE "CINEMA".movies
(
    movie_id       SERIAL PRIMARY KEY,
    title          VARCHAR(255) NOT NULL,
    original_title VARCHAR(255),
    release_year   INTEGER,
    duration       INTEGER,
    created_at     TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_movie_title ON "CINEMA".movies (title);

CREATE TYPE "CINEMA".attribute_data_type AS ENUM ('text', 'boolean', 'date', 'timestamp', 'float', 'integer');
CREATE TABLE "CINEMA".attribute_types
(
    type_id        SERIAL PRIMARY KEY,
    type_name      VARCHAR(100)        NOT NULL,
    data_type      attribute_data_type NOT NULL,
    description    TEXT,
    display_name   VARCHAR(100)        NOT NULL,
    is_marketing   BOOLEAN DEFAULT FALSE,
    is_operational BOOLEAN DEFAULT FALSE
);
CREATE INDEX idx_attribute_type_name ON "CINEMA".attribute_types (type_name);
CREATE INDEX idx_attribute_data_type ON "CINEMA".attribute_types (data_type);

CREATE TABLE "CINEMA".attributes
(
    attribute_id   SERIAL PRIMARY KEY,
    type_id        INTEGER      NOT NULL,
    attribute_name VARCHAR(100) NOT NULL,
    description    TEXT,
    display_name   VARCHAR(100) NOT NULL,
    FOREIGN KEY (type_id) REFERENCES "CINEMA".attribute_types (type_id)
);
CREATE INDEX idx_attribute_name ON "CINEMA".attributes (attribute_name);
CREATE INDEX idx_attribute_type ON "CINEMA".attributes (type_id);

CREATE TABLE "CINEMA".attribute_values
(
    value_id        SERIAL PRIMARY KEY,
    movie_id        INTEGER NOT NULL,
    attribute_id    INTEGER NOT NULL,
    text_value      TEXT,
    boolean_value   BOOLEAN,
    date_value      DATE,
    timestamp_value TIMESTAMP WITH TIME ZONE,
    float_value     DOUBLE PRECISION,
    integer_value   INTEGER,
    created_at      TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES "CINEMA".movies (movie_id),
    FOREIGN KEY (attribute_id) REFERENCES "CINEMA".attributes (attribute_id)
);
CREATE INDEX idx_value_movie ON "CINEMA".attribute_values (movie_id);
CREATE INDEX idx_value_attribute ON "CINEMA".attribute_values (attribute_id);
CREATE INDEX idx_date_value ON "CINEMA".attribute_values (date_value);
CREATE INDEX idx_boolean_value ON "CINEMA".attribute_values (boolean_value);