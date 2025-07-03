-- View сборки служебных данных
CREATE OR REPLACE VIEW operational_tasks AS
SELECT
    m.title,
    STRING_AGG(DISTINCT
        CASE WHEN av.date_value = CURRENT_DATE THEN a.display_name || ' (сегодня)'
        END, ', '
    ) AS today_tasks,
    STRING_AGG(DISTINCT
        CASE WHEN av.date_value BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL '20 days')
             AND av.date_value != CURRENT_DATE THEN a.display_name || ' (' || av.date_value || ')'
        END, ', '
    ) AS upcoming_20days_tasks
FROM
    "CINEMA".movies m
LEFT JOIN
    "CINEMA".attribute_values av ON m.movie_id = av.movie_id
LEFT JOIN
    "CINEMA".attributes a ON av.attribute_id = a.attribute_id
LEFT JOIN
    "CINEMA".attribute_types at ON a.type_id = at.type_id
WHERE
    at.is_operational = TRUE
    AND av.date_value BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL '20 days')
GROUP BY
    m.title;

-- View сборки данных для маркетинга
CREATE OR REPLACE VIEW marketing_data AS
SELECT
    m.title AS film,
    at.display_name AS attribute_type,
    a.display_name AS attribute,
    CASE
        WHEN at.data_type = 'text' THEN av.text_value
        WHEN at.data_type = 'boolean' THEN CASE WHEN av.boolean_value THEN 'Да' ELSE 'Нет' END
        WHEN at.data_type = 'date' THEN TO_CHAR(av.date_value, 'DD.MM.YYYY')
        WHEN at.data_type = 'timestamp' THEN TO_CHAR(av.timestamp_value, 'DD.MM.YYYY HH24:MI')
        WHEN at.data_type = 'float' THEN TO_CHAR(av.float_value, 'FM999999990.00')
        WHEN at.data_type = 'integer' THEN av.integer_value::TEXT
        ELSE NULL
        END AS value_text
FROM
    "CINEMA".movies m
        JOIN
    "CINEMA".attribute_values av ON m.movie_id = av.movie_id
        JOIN
    "CINEMA".attributes a ON av.attribute_id = a.attribute_id
        JOIN
    "CINEMA".attribute_types at ON a.type_id = at.type_id
WHERE
    at.is_marketing = TRUE
ORDER BY
    m.title, at.display_name, a.display_name;