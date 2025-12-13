-- создаем таблицу с фильмами
CREATE TABLE film (
    id SERIAL PRIMARY KEY,
    name VARCHAR(256)
);

-- создаем таблицу с типами атрибутов
CREATE TABLE attribute_type (
    id SERIAL PRIMARY KEY,
    type_name VARCHAR(26) NOT NULL UNIQUE
);

-- создаем таблицу с атрибутами
CREATE TABLE attribute (
    id SERIAL PRIMARY KEY,
    attribute_type_id INT NOT NULL REFERENCES attribute_type(id) ON DELETE CASCADE,
    name VARCHAR(128)
);

-- создаем таблицу с данными
CREATE TABLE value (
    id SERIAL PRIMARY KEY,
    film_id INT NOT NULL REFERENCES film(id) ON DELETE CASCADE,
    attribute_id INT NOT NULL REFERENCES attribute(id) ON DELETE CASCADE,
    value_text TEXT,
    value_bool BOOLEAN,
    value_date DATE,
    value_float FLOAT
);

-- Создаем индексы
CREATE INDEX idx_attribute_type_id ON attribute(attribute_type_id);
CREATE INDEX idx_value_film_id ON value(film_id);
CREATE INDEX idx_value_attribute_id ON value(attribute_id);
CREATE INDEX idx_value_film_attr ON value(film_id, attribute_id);

-- View сборки служебных данных
CREATE VIEW service_data AS
SELECT
    f.name AS film,
    CASE WHEN v.value_date = CURRENT_DATE THEN a.name END AS tasks_today,
    CASE WHEN v.value_date = CURRENT_DATE + INTERVAL '20 day' THEN a.name END AS tasks_in_20_days
        FROM film f
        JOIN value v ON f.id = v.film_id
        JOIN attribute a ON v.attribute_id = a.id
        JOIN attribute_type at ON a.attribute_type_id = at.id
        WHERE at.type_name = 'date';

-- View сборки данных для маркетинга
CREATE VIEW marketing_data AS
SELECT
    f.name AS film,
    at.type_name AS attribute_type,
    a.name AS attribute,
    CASE at.type_name
        WHEN 'text' THEN v.value_text
        WHEN 'bool' THEN v.value_bool::text
        WHEN 'date' THEN v.value_date::text
        WHEN 'float' THEN v.value_float::text
        END AS value
FROM film f
JOIN value v ON f.id = v.film_id
JOIN attribute a ON v.attribute_id = a.id
JOIN attribute_type at ON a.attribute_type_id = at.id;
