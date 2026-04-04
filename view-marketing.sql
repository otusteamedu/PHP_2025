CREATE VIEW marketing_data AS
SELECT
    m.title AS "Фильм",
    at.name AS "Тип атрибута",
    a.name AS "Атрибут",
    COALESCE(
            ma.value_text::text,
            ma.value_boolean::text,
            ma.value_date::text,
            '—'
    ) AS "Значение"
FROM movie_attributes ma
         JOIN attributes a ON ma.attribute_id = a.id
         JOIN attribute_types at ON a.type_id = at.id
JOIN movies m ON ma.movie_id = m.id;
