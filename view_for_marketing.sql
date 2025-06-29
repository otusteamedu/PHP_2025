CREATE VIEW marketing_data AS
SELECT f.title AS film, 
       at.type_name AS attribute_type, 
       a.attribute_name AS attribute, 
       COALESCE(v.value_text, v.value_date::TEXT, v.value_boolean::TEXT, v.value_float::TEXT, v.value_int::TEXT) AS value
FROM films f
JOIN values v ON f.film_id = v.film_id
JOIN attributes a ON v.attribute_id = a.attribute_id
JOIN attribute_types at ON a.type_id = at.type_id;
