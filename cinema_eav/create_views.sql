-- view для служебных задач
CREATE VIEW v_service_tasks AS
SELECT
    f.title AS film,
    STRING_AGG(a.attribute_name, ', ')
               FILTER (WHERE fav.date_value = CURRENT_DATE) AS tasks_today,
    STRING_AGG(a.attribute_name, ', ')
        FILTER (WHERE fav.date_value = CURRENT_DATE + INTERVAL '20 days') AS tasks_in_20_days
FROM films f
         JOIN attribute_values fav  ON fav.film_id       = f.film_id
         JOIN attributes       a    ON a.attribute_id    = fav.attribute_id
         JOIN attribute_types  at   ON at.type_id        = a.type_id
WHERE at.type_name = 'service_date'
GROUP BY f.title;

-- view для маркетинга
CREATE VIEW v_marketing_data AS
SELECT
    f.title          AS film,
    at.type_name     AS attribute_type,
    a.attribute_name AS attribute_name,
    COALESCE(
            fav.text_value,
            fav.boolean_value::text,
            fav.date_value::text,
            fav.numeric_value::text
    ) AS value_text
FROM films f
         JOIN attribute_values fav ON fav.film_id    = f.film_id
         JOIN attributes       a   ON a.attribute_id = fav.attribute_id
         JOIN attribute_types  at  ON at.type_id     = a.type_id;

-- получаем результаты
SELECT * FROM v_service_tasks;
SELECT * FROM v_marketing_data;