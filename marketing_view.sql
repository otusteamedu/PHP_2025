CREATE VIEW marketing_view AS
SELECT
    m.name as movie_name,
    t.name as attribute_type_name,
    a.name as attribute_name,
    COALESCE(
        v.int_value::text,
        v.numeric_value::text,
        v.boolean_value::text,
        v.date_value::text,
        v.text_value
    ) as attribute_value
FROM attribute_values v
    JOIN movies m ON m.id = v.movie_id
    JOIN attributes a ON a.id = v.attribute_id
    JOIN attribute_types t ON t.id = a.type_id
ORDER BY movie_name;