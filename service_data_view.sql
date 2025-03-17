CREATE VIEW service_data_view AS
SELECT films.name       AS film_name,
       STRING_AGG(CASE
                      WHEN (attribute_values.date_value = CURRENT_DATE)
                          THEN attributes.name || ': ' || attribute_values.date_value END, '; ') AS tasks_today,
       STRING_AGG(CASE
                      WHEN (attribute_values.date_value = (CURRENT_DATE + INTERVAL '20 DAYS'))
                          THEN attributes.name || ': ' || attribute_values.date_value END, '; ') AS tasks_20_days
FROM films
        JOIN values AS attribute_values v ON attribute_values.film_id = films.id
        JOIN attributes ON attributes.id = attribute_values.attribute_id
        JOIN attribute_types ON attribute_types.id = attributes.type_id
WHERE attribute_types.name = 'Мировая премьера'
GROUP BY film_name;