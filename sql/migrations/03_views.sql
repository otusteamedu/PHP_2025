CREATE OR REPLACE VIEW vw_service_tasks AS
SELECT
    m.title AS movie,
    COALESCE(
            STRING_AGG(a.name, ', ' ORDER BY a.name)
            FILTER (WHERE av.value_date = CURRENT_DATE),
            '-'
    ) AS tasks_today,
    COALESCE(
            STRING_AGG(a.name, ', ' ORDER BY a.name)
            FILTER (WHERE av.value_date = CURRENT_DATE + 20),
            '-'
    ) AS tasks_in_20_days
FROM movies m
         LEFT JOIN attribute_values av
                   ON av.movie_id = m.id
         LEFT JOIN attributes a
                   ON a.id = av.attribute_id
         LEFT JOIN attribute_types at
ON at.id = a.attribute_type_id
    AND at.code = 'service_date'
GROUP BY m.id, m.title
ORDER BY m.title;

CREATE OR REPLACE VIEW vw_marketing_export AS
SELECT
    m.title AS movie,
    at.name AS attribute_type,
    a.name AS attribute,
    CASE a.data_type
        WHEN 'text' THEN av.value_text
        WHEN 'boolean' THEN CASE WHEN av.value_boolean THEN 'Да' ELSE 'Нет' END
        WHEN 'date' THEN TO_CHAR(av.value_date, 'YYYY-MM-DD')
        WHEN 'numeric' THEN TRIM(TO_CHAR(av.value_numeric, 'FM999999990.9999'))
        END AS value
FROM attribute_values av
    JOIN movies m
ON m.id = av.movie_id
    JOIN attributes a
    ON a.id = av.attribute_id
    JOIN attribute_types at
    ON at.id = a.attribute_type_id
ORDER BY m.title, at.name, a.name;
