-- Дюна: Часть вторая
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT m.id, a.id, 'Масштабное и визуально сильное кино'
FROM movies m, attributes a
WHERE m.title = 'Дюна: Часть вторая'
  AND a.code = 'critics_review';

INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
SELECT m.id, a.id, TRUE
FROM movies m, attributes a
WHERE m.title = 'Дюна: Часть вторая'
  AND a.code = 'oscar';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, DATE '2024-02-06'
FROM movies m, attributes a
WHERE m.title = 'Дюна: Часть вторая'
  AND a.code = 'world_premiere';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, CURRENT_DATE
FROM movies m, attributes a
WHERE m.title = 'Дюна: Часть вторая'
  AND a.code = 'tv_ads_start';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, CURRENT_DATE + 20
FROM movies m, attributes a
WHERE m.title = 'Дюна: Часть вторая'
  AND a.code = 'ticket_sales_start';

INSERT INTO attribute_values (movie_id, attribute_id, value_numeric)
SELECT m.id, a.id, 8.7000
FROM movies m, attributes a
WHERE m.title = 'Дюна: Часть вторая'
  AND a.code = 'kp_rating';

-- Мастер и Маргарита
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT m.id, a.id, 'Необычная авторская интерпретация классического романа'
FROM movies m, attributes a
WHERE m.title = 'Мастер и Маргарита'
  AND a.code = 'academy_review';

INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
SELECT m.id, a.id, TRUE
FROM movies m, attributes a
WHERE m.title = 'Мастер и Маргарита'
  AND a.code = 'nika';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, DATE '2024-01-25'
FROM movies m, attributes a
WHERE m.title = 'Мастер и Маргарита'
  AND a.code = 'ru_premiere';

INSERT INTO attribute_values (movie_id, attribute_id, value_numeric)
SELECT m.id, a.id, 7.9000
FROM movies m, attributes a
WHERE m.title = 'Мастер и Маргарита'
  AND a.code = 'kp_rating';

-- Оппенгеймер
INSERT INTO attribute_values (movie_id, attribute_id, value_text)
SELECT m.id, a.id, 'Исторически выверенная и напряженная драма'
FROM movies m, attributes a
WHERE m.title = 'Оппенгеймер'
  AND a.code = 'critics_review';

INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
SELECT m.id, a.id, TRUE
FROM movies m, attributes a
WHERE m.title = 'Оппенгеймер'
  AND a.code = 'oscar';

INSERT INTO attribute_values (movie_id, attribute_id, value_date)
SELECT m.id, a.id, DATE '2023-07-21'
FROM movies m, attributes a
WHERE m.title = 'Оппенгеймер'
  AND a.code = 'world_premiere';

INSERT INTO attribute_values (movie_id, attribute_id, value_numeric)
SELECT m.id, a.id, 8.4000
FROM movies m, attributes a
WHERE m.title = 'Оппенгеймер'
  AND a.code = 'imdb_rating';
