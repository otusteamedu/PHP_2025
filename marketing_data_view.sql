CREATE VIEW marketing_data_view AS
SELECT films.title          AS film_title,
       attribute_types.name AS attribute_type_name,
       attributes.name      AS attribute_name,
       COALESCE(
               attribute_values.text_value,
               attribute_values.boolean_value::text,
               attribute_values.date_value::text,
               attribute_values.string_value,
               attribute_values.int_value::text,
               attribute_values.numeric_value::text)
                            AS attribute_value
FROM values AS attribute_values
         JOIN films ON films.id = attribute_values.film_id
         JOIN attributes ON attributes.id = attribute_values.attribute_id
         JOIN attribute_types ON attribute_types.id = attributes.type_id
ORDER BY film_name;