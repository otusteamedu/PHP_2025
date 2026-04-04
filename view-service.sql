CREATE VIEW service_tasks AS
SELECT
    m.title AS "Фильм",
    STRING_AGG(CASE
                   WHEN ma.value_date = CURRENT_DATE THEN a.name
                   END, ', ') AS "Задачи на сегодня",
    STRING_AGG(CASE
                   WHEN ma.value_date >= CURRENT_DATE + INTERVAL '20 days' THEN a.name
                   END, ', ') AS "Задачи через 20 дней"
FROM movie_attributes ma
         JOIN attributes a ON ma.attribute_id = a.id
         JOIN attribute_types at ON a.type_id = at.id
         JOIN movies m ON ma.movie_id = m.id
WHERE at.name = 'Служебные даты'
GROUP BY m.title;
