CREATE VIEW tasks_view AS
SELECT movie.name,
    CASE
       WHEN mpv.value_date = CURRENT_DATE
           THEN attribute.name
       ELSE NULL
       END
       AS "Задачи на текущий день",
    CASE
       WHEN mpv.value_date = CURRENT_DATE + 20
           THEN attribute.name
       ELSE NULL
       END
       AS "Задачи через 20 дней"
FROM movie_props_value mpv
    INNER JOIN "attribute" ON mpv.attribute_id = attribute.id
    INNER JOIN "movie" ON mpv.movie_id = movie.id
    INNER JOIN "type_attribute" ta ON ta.id = attribute.id_type_attribute AND "ta"."name" = 'Служебные даты'
WHERE mpv.value_date = CURRENT_DATE + 20
   or mpv.value_date = CURRENT_DATE;


CREATE VIEW marketing_data AS
SELECT movie.name "назавние фильма",
       t.name AS "тип атрибута",
       attribute."name" AS "атрибут",
       COALESCE (
               m."value_string",
               CAST(m."value_date" AS text),
               CAST(m."value_boolean" AS text),
               CAST(m."value_integer" AS text),
               CAST(m."value_float" AS text)
       ) AS value
FROM movie_props_value m
     INNER JOIN  "attribute" ON m.attribute_id = attribute.id
     INNER JOIN  "type_attribute" t ON t.id = attribute.id_type_attribute
     INNER JOIN  "movie" ON m.movie_id = movie.id



