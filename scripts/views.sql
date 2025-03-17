CREATE OR REPLACE VIEW service_tasks AS
SELECT
    m.name AS film,
    a.name AS task,
    v.value_date AS task_date,
    CASE
        WHEN v.value_date = CURRENT_DATE THEN 'Сегодня'
        WHEN v.value_date = CURRENT_DATE + INTERVAL '20 days' THEN 'Через 20 дней'
    END AS task_status
FROM
    movies m
JOIN values v ON m.id = v.movie_id
JOIN attributes a ON v.attribute_id = a.id
JOIN attribute_types at ON a.attribute_type_id = at.id
WHERE
    at.name = 'служебные даты'
    AND (v.value_date = CURRENT_DATE OR v.value_date = CURRENT_DATE + INTERVAL '20 days');

CREATE OR REPLACE VIEW marketing_data AS
SELECT
    m.name AS film,
    at.name AS attribute_type,
    a.name AS attribute,
    CASE
        WHEN at.data_type = 'TEXT' THEN v.value_text
        WHEN at.data_type = 'BOOLEAN' THEN CAST(v.value_boolean AS TEXT)
        WHEN at.data_type = 'DATE' THEN CAST(v.value_date AS TEXT)
        WHEN at.data_type = 'INT' THEN CAST(v.value_int AS TEXT)
        WHEN at.data_type = 'FLOAT' THEN CAST(v.value_float AS TEXT)
    END AS value
FROM
    movies m
JOIN values v ON m.id = v.movie_id
JOIN attributes a ON v.attribute_id = a.id
JOIN attribute_types at ON a.attribute_type_id = at.id;
