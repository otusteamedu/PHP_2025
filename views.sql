CREATE OR REPLACE VIEW view_service_tasks AS
SELECT
    t.id AS title_id,
    t.title,
    a.name AS task_name,
    av.value_date AS task_date,
    CASE
        WHEN av.value_date = CURRENT_DATE THEN 'Актуально сегодня'
        WHEN av.value_date = CURRENT_DATE + INTERVAL '20 days' THEN 'Через 20 дней'
    ELSE NULL
END AS relevance
FROM attribute_values av
JOIN attributes a ON av.attribute_id = a.id
JOIN attribute_types at ON a.type_id = at.id
JOIN titles t ON av.title_id = t.id
WHERE at.name = 'Служебные даты'
  AND av.value_date BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '20 days';

CREATE OR REPLACE VIEW view_marketing_data AS
SELECT
    t.title,
    at.name AS type_name,
    a.name AS attribute_name,
    COALESCE(
            av.value_text,
            av.value_date::text,
            CASE WHEN av.value_boolean THEN 'Да' ELSE 'Нет' END
    ) AS value_display
FROM attribute_values av
         JOIN attributes a ON av.attribute_id = a.id
         JOIN attribute_types at ON a.type_id = at.id
    JOIN titles t ON av.title_id = t.id;
