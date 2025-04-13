-- сущности / entity
CREATE TABLE movies (
    id      SERIAL PRIMARY KEY,
    title   VARCHAR(255) NOT NULL
);

-- типы данных атрибутов / data types
CREATE TABLE attribute_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,   

    -- ограничим список возможных типов, что бы не записать неправильный тип
    data_type VARCHAR(50) NOT NULL CHECK (data_type IN ('TEXT', 'BOOLEAN', 'DATE', 'INT', 'FLOAT')) 
    
);

-- атрибуты / attribute definitions
CREATE TABLE attributes (
    id                  SERIAL PRIMARY KEY,
    name                VARCHAR(255) NOT NULL,
    attribute_type_id   INT NOT NULL REFERENCES attribute_types(id)
);

-- значения атрибутов / entity-attribute-value
CREATE TABLE eav_values (
    id              SERIAL PRIMARY KEY,
    movie_id        INT NOT NULL REFERENCES movies(id) ON DELETE CASCADE,
    attribute_id    INT NOT NULL REFERENCES attributes(id) ON DELETE CASCADE,

    value_string    VARCHAR(255) NULL,
    value_bool      BOOLEAN NULL,
    value_date      DATE  NULL,
    value_int       INT  NULL,
    value_float     REAL  NULL,

    -- проверка на уникальность: у одного фильма не может быть два значения одного атрибута
    UNIQUE (movie_id, attribute_id)
);

-- индексы: поиск всех значений по фильму
CREATE INDEX idx_eav_movie_id ON eav_values(movie_id);

-- индексы: поиск всех значений по атрибуту
CREATE INDEX idx_eav_attribute_id ON eav_values(attribute_id);