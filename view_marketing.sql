CREATE VIEW view_marketing AS
SELECT f.name,
       at."name" AS type_attribute,
       a.name    AS attribute,
       COALESCE(
               cast(v.value_bool AS varchar),
               cast(v.value_date AS varchar),
               cast(v.value_float AS varchar),
               v.value_text
       ) AS value
        FROM films AS f
    JOIN attribute_values AS v ON f.id = v.film_id
    JOIN attributes AS a ON a.id = v.attibute_id
    JOIN attribute_types AS at ON at.id = a.type_id
