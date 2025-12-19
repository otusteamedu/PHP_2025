CREATE VIEW service_view AS
SELECT
    m.title AS "фильм",
    STRING_AGG(
        CASE
            WHEN DATE(v.datetime_val) = CURRENT_DATE THEN a.name || ': ' || v.datetime_val
            ELSE NULL
        END,
        '; '
    ) AS "задачи_на_сегодня",
    STRING_AGG(
        CASE
            WHEN DATE(v.datetime_val) = CURRENT_DATE + INTERVAL '20 days' THEN a.name || ': ' || v.datetime_val
            ELSE NULL
        END,
        '; '
    ) AS "задачи_на_20_дней"
FROM movies m
JOIN values v ON m.id = v.movie_id
JOIN attributes a ON v.attribute_id = a.id
JOIN attr_types at ON a.attr_type_id = at.id
WHERE at.name = 'service_dates'
    AND DATE(v.datetime_val) IN (CURRENT_DATE, CURRENT_DATE + INTERVAL '20 days')
GROUP BY m.id
ORDER BY m.title;

CREATE VIEW marketing_view AS
SELECT
    m.title AS "фильм",
    at.name AS "тип_атрибута",
    a.name AS "атрибут",
    CASE
        WHEN at.type = 'string' THEN COALESCE(v.string_val, '')
        WHEN at.type = 'boolean' THEN
            CASE
                WHEN v.boolean_val = TRUE THEN 'Да'
                ELSE 'Нет'
            END
        WHEN at.type = 'datetime' THEN COALESCE(v.datetime_val::TEXT, '')
        WHEN at.type = 'integer' THEN COALESCE(v.integer_val::TEXT, '')
        WHEN at.type = 'numeric' THEN COALESCE(v.numeric_val::TEXT, '')
        ELSE 'Нет данных'
    END AS "значение"
FROM movies m
JOIN values v ON m.id = v.movie_id
JOIN attributes a ON v.attribute_id = a.id
JOIN attr_types at ON a.attr_type_id = at.id
WHERE at.service = FALSE
ORDER BY m.title, at.name, a.name;
