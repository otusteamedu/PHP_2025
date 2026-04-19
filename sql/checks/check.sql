-- ПРОВЕРКА СЫРЫХ ДАННЫХ (как всё устроено под капотом)
SELECT * FROM movies ORDER BY id;

SELECT id, code, name, data_type FROM attributes ORDER BY id;

SELECT * FROM attribute_values ORDER BY movie_id, attribute_id;

--  View служебные задачи на сегодня и через 20 дней
SELECT * FROM vw_service_tasks ORDER BY movie;

-- Выгрузка для маркетологов (все значения приведены к тексту)
SELECT * FROM vw_marketing_export ORDER BY movie, attribute_type, attribute;


-- ЗАПРОСЫ

-- 1: Найти все фильмы, которые получили премию Оскар (работа с boolean)
SELECT m.title AS "Оскароносные фильмы"
FROM attribute_values av
         JOIN movies m ON m.id = av.movie_id
         JOIN attributes a ON a.id = av.attribute_id
WHERE a.code = 'oscar'
  AND av.value_boolean = TRUE;

-- 2: Вывести топ фильмов по рейтингу Кинопоиска (работа с numeric и сортировкой)
SELECT
    m.title AS "Фильм",
    av.value_numeric AS "Рейтинг КП"
FROM attribute_values av
         JOIN movies m ON m.id = av.movie_id
         JOIN attributes a ON a.id = av.attribute_id
WHERE a.code = 'kp_rating'
ORDER BY av.value_numeric DESC NULLS LAST;

-- 3: Найти фильмы, чья мировая премьера была до 2024 года (работа с date)
SELECT
    m.title AS "Фильм",
    av.value_date AS "Дата мировой премьеры"
FROM attribute_values av
         JOIN movies m ON m.id = av.movie_id
         JOIN attributes a ON a.id = av.attribute_id
WHERE a.code = 'world_premiere'
  AND av.value_date < '2024-01-01'
ORDER BY av.value_date;
