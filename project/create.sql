-- Таблица фильмов
CREATE TABLE movies (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT
);

-- Таблица типов атрибутов
CREATE TABLE attribute_types (
    id SERIAL PRIMARY KEY,
    type_name VARCHAR(100) NOT NULL,
    data_type VARCHAR(20) NOT NULL CHECK (data_type IN ('TEXT', 'BOOLEAN', 'DATE', 'NUMERIC'))
);

-- Таблица атрибутов
CREATE TABLE attributes (
    id SERIAL PRIMARY KEY,
    attribute_name VARCHAR(100) NOT NULL,
    type_id INT NOT NULL REFERENCES attribute_types(id)
);

-- Таблица значений атрибутов
CREATE TABLE attribute_values (
    id SERIAL PRIMARY KEY,
    movie_id INT NOT NULL REFERENCES movies(id),
    attribute_id INT NOT NULL REFERENCES attributes(id),
    value_text TEXT,
    value_boolean BOOLEAN,
    value_date DATE,
    value_numeric NUMERIC(10, 2), -- Для хранения чисел с фиксированной точностью
    CONSTRAINT check_value_type CHECK (
        (value_text IS NOT NULL AND value_boolean IS NULL AND value_date IS NULL AND value_numeric IS NULL) OR
        (value_text IS NULL AND value_boolean IS NOT NULL AND value_date IS NULL AND value_numeric IS NULL) OR
        (value_text IS NULL AND value_boolean IS NULL AND value_date IS NOT NULL AND value_numeric IS NULL) OR
        (value_text IS NULL AND value_boolean IS NULL AND value_date IS NULL AND value_numeric IS NOT NULL)
    )
);

CREATE INDEX idx_attribute_values_movie_id ON attribute_values (movie_id);
CREATE INDEX idx_attribute_values_attribute_id ON attribute_values (attribute_id);
CREATE INDEX idx_attribute_values_date ON attribute_values (value_date);

CREATE VIEW service_data AS
SELECT
    m.title AS movie_title,
    a.attribute_name AS task,
    av.value_date AS task_date,
    CASE
        WHEN av.value_date = CURRENT_DATE THEN 'Today'
        WHEN av.value_date = CURRENT_DATE + INTERVAL '20 days' THEN 'In 20 days'
        ELSE NULL
END AS status
FROM movies m
JOIN attribute_values av ON m.id = av.movie_id
JOIN attributes a ON av.attribute_id = a.id
JOIN attribute_types at ON a.type_id = at.id
WHERE at.type_name = 'служебные даты'
AND (av.value_date = CURRENT_DATE OR av.value_date = CURRENT_DATE + INTERVAL '20 days');

CREATE VIEW marketing_data AS
SELECT
    m.title AS movie_title,
    at.type_name AS attribute_type,
    a.attribute_name AS attribute,
    COALESCE(
            av.value_text,
            CAST(av.value_boolean AS TEXT),
            CAST(av.value_date AS TEXT),
            CAST(av.value_numeric AS TEXT)
    ) AS value
FROM movies m
JOIN attribute_values av ON m.id = av.movie_id
JOIN attributes a ON av.attribute_id = a.id
JOIN attribute_types at ON a.type_id = at.id;