-- Создание таблицы фильмов
CREATE TABLE films (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

-- Создание таблицы типов атрибутов
CREATE TABLE attribute_types (
    id SERIAL PRIMARY KEY,
    type_name VARCHAR(255) NOT NULL UNIQUE
);

-- Создание таблицы атрибутов
CREATE TABLE attributes (
    id SERIAL PRIMARY KEY,
    attribute_name VARCHAR(255) NOT NULL,
    type_id INT,
    FOREIGN KEY (type_id) REFERENCES attribute_types(id)
);

-- Создание таблицы значений
CREATE TABLE values (
    id SERIAL PRIMARY KEY,
    film_id INT,
    attribute_id INT,
    text_value TEXT,
    boolean_value BOOLEAN,
    date_value DATE,
    numeric_value NUMERIC(3, 1),
    FOREIGN KEY (film_id) REFERENCES films(id),
    FOREIGN KEY (attribute_id) REFERENCES attributes(id)
);

-- Индексы для ускорения выборок
CREATE INDEX idx_values_film_attribute ON values (film_id, attribute_id);
CREATE INDEX idx_attributes_type_id ON attributes (type_id);
CREATE INDEX idx_values_date_value ON values (date_value);

-- Заполнение данными
INSERT INTO films (title) VALUES ('Начало'), ('Интерстеллар');

INSERT INTO attribute_types (type_name) VALUES ('Рецензии'), ('Премия'), ('Важные даты'), ('Служебные даты'), ('Рейтинги');

INSERT INTO attributes (attribute_name, type_id) VALUES
('Рецензии критиков', 1),
('Отзыв неизвестной киноакадемии', 1),
('Оскар', 2),
('Ника', 2),
('Мировая премьера', 3),
('Премьера в РФ', 3),
('Начало продажи билетов', 4),
('Запуск рекламы на ТВ', 4),
('Оценка на кинопоиске', 5);

-- Значения для фильма "Начало"
INSERT INTO values (film_id, attribute_id, text_value) VALUES (1, 1, 'Отличный фильм!');
INSERT INTO values (film_id, attribute_id, boolean_value) VALUES (1, 3, true);
INSERT INTO values (film_id, attribute_id, date_value) VALUES (1, 5, '2010-07-16');
INSERT INTO values (film_id, attribute_id, date_value) VALUES (1, 7, '2025-11-20');
INSERT INTO values (film_id, attribute_id, date_value) VALUES (1, 8, '2025-10-31');
INSERT INTO values (film_id, attribute_id, numeric_value) VALUES (1, 9, 8.7);


-- Значения для фильма "Интерстеллар"
INSERT INTO values (film_id, attribute_id, text_value) VALUES (2, 2, 'Спорный, но интересный.');
INSERT INTO values (film_id, attribute_id, boolean_value) VALUES (2, 4, false);
INSERT INTO values (film_id, attribute_id, date_value) VALUES (2, 6, '2014-11-07');
INSERT INTO values (film_id, attribute_id, date_value) VALUES (2, 7, '2025-11-25');
INSERT INTO values (film_id, attribute_id, date_value) VALUES (2, 8, '2025-11-15');
INSERT INTO values (film_id, attribute_id, numeric_value) VALUES (2, 9, 8.6);


-- View для служебных данных
CREATE OR REPLACE VIEW service_data AS
SELECT
    f.title AS "фильм",
    string_agg(CASE WHEN v.date_value = CURRENT_DATE THEN a.attribute_name END, ', ') AS "задачи актуальные на сегодня",
    string_agg(CASE WHEN v.date_value = CURRENT_DATE + INTERVAL '20 day' THEN a.attribute_name END, ', ') AS "задачи актуальные через 20 дней"
FROM films f
JOIN values v ON f.id = v.film_id
JOIN attributes a ON v.attribute_id = a.id
JOIN attribute_types at ON a.type_id = at.id
WHERE at.type_name = 'Служебные даты'
GROUP BY f.title;

-- View для маркетинговых данных
CREATE OR REPLACE VIEW marketing_data AS
SELECT
    f.title AS "фильм",
    at.type_name AS "тип атрибута",
    a.attribute_name AS "атрибут",
    COALESCE(v.text_value, v.boolean_value::TEXT, v.date_value::TEXT, v.numeric_value::TEXT) AS "значение"
FROM films f
JOIN values v ON f.id = v.film_id
JOIN attributes a ON v.attribute_id = a.id
JOIN attribute_types at ON a.type_id = at.id;
