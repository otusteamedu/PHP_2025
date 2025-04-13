--view для служебных данных
CREATE OR REPLACE VIEW movie_tasks_view AS
SELECT
    m.title AS "фильм",
    STRING_AGG(  -- список задач на сегодня, объединённый в одну строку через запятую
        CASE WHEN ev.value_date = CURRENT_DATE THEN a.name END,
        ', '
    ) FILTER (WHERE ev.value_date = CURRENT_DATE) AS "задачи актуальные на сегодня",
    STRING_AGG(  -- тоже самое через 20 дней
        CASE WHEN ev.value_date = CURRENT_DATE + INTERVAL '20 days' THEN a.name END,
        ', '
    ) FILTER (WHERE ev.value_date = CURRENT_DATE + INTERVAL '20 days') AS "задачи актуальные через 20 дней"
FROM eav_values ev
JOIN movies m ON ev.movie_id = m.id
JOIN attributes a ON ev.attribute_id = a.id
JOIN attribute_types at ON a.attribute_type_id = at.id
WHERE at.name = 'служебные даты'
GROUP BY m.id, m.title;

--view для маркетинга
CREATE OR REPLACE VIEW movie_marketing_view AS
SELECT
    m.title AS "фильм",
    at.data_type AS "тип атрибута",
    a.name AS "атрибут",
    CASE at.data_type
        WHEN 'TEXT'   THEN ev.value_string
        WHEN 'BOOLEAN' THEN ev.value_bool::TEXT
        WHEN 'DATE'    THEN ev.value_date::TEXT
        WHEN 'INT'     THEN ev.value_int::TEXT
        WHEN 'FLOAT'   THEN ROUND(ev.value_float, 2)::TEXT --округлим чтобы не получить длинную дробь при выводе
        ELSE NULL
    END AS "значение"
FROM eav_values ev
JOIN movies m ON ev.movie_id = m.id
JOIN attributes a ON ev.attribute_id = a.id
JOIN attribute_types at ON a.attribute_type_id = at.id;