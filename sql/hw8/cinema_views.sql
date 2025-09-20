SET search_path TO cinema_eav;

CREATE OR REPLACE VIEW actual_tasks AS
    SELECT
        concat(m.name, ' (', m.year, ')') AS movie_name,
        string_agg(CASE WHEN av.date_value = current_date THEN a.attribute_name END, ', ') AS tasks_for_today,
        string_agg(CASE WHEN av.date_value <> current_date THEN a.attribute_name END, ', ') AS tasks_after_20_days
    FROM
        movie m
    JOIN attribute_value av ON m.id = av.movie_id
    JOIN attribute a ON a.id = av.attribute_id
    JOIN attribute_type at ON at.id = a.attribute_type_id
    WHERE
        at.type_name = 'служебные даты'
        AND (av.date_value = current_date OR av.date_value >= current_date + INTERVAL '20 days')
    GROUP BY m.id, m.name, m.year;

CREATE OR REPLACE VIEW marketing_data AS
    SELECT
        m.name AS movie_name,
        at.type_name AS attribute_type,
        a.attribute_name,
        coalesce(
            av.text_value,
            CASE WHEN av.boolean_value IS NOT NULL THEN
                CASE WHEN av.boolean_value THEN 'Да' ELSE 'Нет' END
            END,
            CASE WHEN av.date_value IS NOT NULL THEN
                to_char(av.date_value, 'DD.MM.YYYY')
            END,
            av.integer_value::text,
            av.float_value::text
        ) AS value
    FROM
        movie m
    JOIN attribute_value av ON m.id = av.movie_id
    JOIN attribute a ON a.id = av.attribute_id
    JOIN attribute_type at ON at.id = a.attribute_type_id
    WHERE
        at.type_name <> 'служебные даты'
    ORDER BY m.id, at.id;
