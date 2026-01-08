CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    movie_name VARCHAR(50) NOT NULL
);

CREATE TABLE attribute_types (
	id INT PRIMARY KEY AUTO_INCREMENT,
	attribute_type_name VARCHAR(50) NOT NULL
);

CREATE TABLE attributes (
	id INT PRIMARY KEY AUTO_INCREMENT,
	attribute_name VARCHAR(50) NOT NULL
);

CREATE TABLE movie_values (
	id INT PRIMARY KEY AUTO_INCREMENT,
	movie_id INT NOT NULL,
	FOREIGN KEY (movie_id) REFERENCES movies(id),
	attribute_type_id INT NOT NULL,
	FOREIGN KEY (attribute_type_id) REFERENCES attribute_types(id),
	attribute_id INT NOT NULL,
	FOREIGN KEY (attribute_id) REFERENCES attributes(id),
	value_text TEXT,
	value_date DATETIME,
	value_integer INT,
	value_boolean BOOLEAN
);

CREATE INDEX idx_values_movie_id ON movie_values(movie_id);
CREATE INDEX idx_values_attribute_type_id ON movie_values(attribute_type_id);
CREATE INDEX idx_values_attribute_id ON movie_values(attribute_id);
CREATE INDEX idx_movie_attribute ON movie_values(movie_id, attribute_type_id, attribute_id);

INSERT INTO movies (movie_name) VALUES
('Компьютерщики'),
('Дюна 3'),
('Возвращение'),
('Властелин колец'),
('Сильмариллион');

INSERT INTO attribute_types (attribute_type_name) VALUES
('Рецензии'),
('Важные даты'),
('Служебная дата'),
('Цена');

INSERT INTO attributes (attribute_name) VALUES
('Рецензия зрителя'),
('Рецензия киноакадемиков'),
('Дата премьеры в России'),
('Дата продажи билетов'),
('Цена в день премьеры'),
('Цена в обычный день');

INSERT INTO movie_values (movie_id, attribute_type_id, attribute_id, value_text) VALUES
(1, 1, 1, "Ваууу!"),
(1, 1, 2, "Потрясающий сериал"),
(4, 1, 2, "Рецензия академоков");

INSERT INTO movie_values (movie_id, attribute_type_id, attribute_id, value_date) VALUES
(1, 2, 3, '2006-01-01'),
(1, 3, 4, '2026-01-20'),
(5, 3, 4, '2026-01-8'),
(4, 3, 4, '2016-01-15'),
(4, 2, 3, '2015-01-28');

INSERT INTO movie_values (movie_id, attribute_type_id, attribute_id, value_integer) VALUES
(5, 4, 5, 100000),
(5, 4, 6, 50000),
(1, 4, 5, 30000),
(1, 4, 6, 30000),
(4, 4, 5, 50000),
(4, 4, 6, 50000);

CREATE OR REPLACE VIEW service_data_view AS
SELECT 
    m.movie_name AS 'Фильм',
    GROUP_CONCAT(
        DISTINCT CASE 
            WHEN mv.value_date = CURDATE() 
            THEN CONCAT(a.attribute_name)
            ELSE NULL 
        END
        SEPARATOR ', '
    ) AS 'Задачи на сегодня',
    GROUP_CONCAT(
        DISTINCT CASE 
            WHEN mv.value_date = DATE_ADD(CURDATE(), INTERVAL 20 DAY)
            THEN CONCAT(a.attribute_name)
            ELSE NULL 
        END
        SEPARATOR ', '
    ) AS 'Задачи через 20 дней'
FROM movies m
LEFT JOIN movie_values mv ON m.id = mv.movie_id
LEFT JOIN attributes a ON mv.attribute_id = a.id
LEFT JOIN attribute_types at ON mv.attribute_type_id = at.id 
    AND at.attribute_type_name = 'Служебная дата'
WHERE (mv.value_date = CURDATE() OR mv.value_date = DATE_ADD(CURDATE(), INTERVAL 20 DAY))
GROUP BY m.id, m.movie_name
ORDER BY m.movie_name;


CREATE OR REPLACE VIEW marketing_data_view AS
SELECT 
    m.movie_name AS 'Фильм',
    at.attribute_type_name AS 'Тип атрибута',
    a.attribute_name AS 'Атрибут',
    CASE 
        WHEN mv.value_text IS NOT NULL THEN mv.value_text
        WHEN mv.value_date IS NOT NULL THEN DATE_FORMAT(mv.value_date, '%d.%m.%Y')
        WHEN mv.value_integer IS NOT NULL AND at.attribute_type_name = 'Цена' 
            THEN CONCAT(
                FORMAT(mv.value_integer / 100, 2),
                ' руб.'
            )
        WHEN mv.value_integer IS NOT NULL THEN CAST(mv.value_integer AS CHAR)
        WHEN mv.value_boolean IS NOT NULL THEN 
            CASE 
                WHEN mv.value_boolean = 1 THEN 'Да' 
                WHEN mv.value_boolean = 0 THEN 'Нет' 
            END
        ELSE 'Нет значения'
    END AS 'Значение'
FROM movies m
LEFT JOIN movie_values mv ON m.id = mv.movie_id
LEFT JOIN attribute_types at ON mv.attribute_type_id = at.id
LEFT JOIN attributes a ON mv.attribute_id = a.id
WHERE mv.id IS NOT NULL 
ORDER BY 
    m.movie_name;