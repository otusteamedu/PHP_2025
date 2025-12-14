-- Служебные данные
CREATE VIEW service_tasks AS
SELECT
    m.title AS "фильм",
    CASE WHEN av.date_value = CURRENT_DATE THEN a.name END AS "задачи актуальные на сегодня",
    CASE WHEN av.date_value = CURRENT_DATE + INTERVAL '20 days' THEN a.name END AS "задачи актуальные через 20 дней"
FROM movie m
    JOIN attribute_value av ON m.id = av.movie_id
    JOIN attribute a ON av.attribute_id = a.id
    JOIN attribute_type at ON a.attribute_type_id = at.id
WHERE at.name = 'Служебные даты'
ORDER BY m.title, av.date_value;

-- Маркетинговые данные
CREATE VIEW marketing_data AS
SELECT
    m.title AS "фильм",
    at.name AS "тип атрибута",
    a.name AS "атрибут",
    COALESCE(
        av.text_value,
        av.date_value::text,
        av.boolean_value::text,
        av.decimal_value::text,
        av.integer_value::text
    ) AS "значение"
FROM movie m
    JOIN attribute_value av ON m.id = av.movie_id
    JOIN attribute a ON av.attribute_id = a.id
    JOIN attribute_type at ON a.attribute_type_id = at.id
ORDER BY m.title, at.name, a.name;
