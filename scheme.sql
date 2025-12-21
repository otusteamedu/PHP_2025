-- Фильмы
CREATE TABLE movie (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    description TEXT
);

CREATE INDEX idx_movie_title ON movie (title);

-- Типы атрибутов
CREATE TYPE data_type_enum AS ENUM ('varchar', 'text', 'int', 'decimal', 'boolean', 'date', 'json');

CREATE TABLE attribute_type (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    data_type data_type_enum NOT NULL
);

CREATE INDEX idx_attr_type_alias ON attribute_type (name);
CREATE INDEX idx_attr_type_data_type ON attribute_type (data_type);

-- Атрибуты
CREATE TABLE attribute (
    id SERIAL PRIMARY KEY,
    attr_type_id INT NOT NULL,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,

    FOREIGN KEY (attr_type_id) REFERENCES attribute_type(id)
);

CREATE INDEX idx_attribute_type ON attribute (attr_type_id);
CREATE INDEX idx_attribute_name ON attribute (name);

-- Значения атрибутов
CREATE TABLE attribute_values (
    id SERIAL PRIMARY KEY,
    movie_id INT NOT NULL,
    attribute_id INT NOT NULL,

    value_varchar VARCHAR(255) NULL,
    value_text TEXT NULL,
    value_int INT NULL,
    value_decimal DECIMAL(10, 2) NULL,
    value_boolean BOOLEAN NULL,
    value_date DATE NULL,
    value_json JSON NULL,

    FOREIGN KEY (movie_id) REFERENCES movie(id),
    FOREIGN KEY (attribute_id) REFERENCES attribute(id)
);   
   
CREATE INDEX idx_values_movie ON attribute_values (movie_id);
CREATE INDEX idx_values_attribute ON attribute_values (attribute_id);
