CREATE VIEW marketing_data AS
SELECT films.title as film,
       attributes.title as attribute,
       attribute_types.code as attribute_type,
       COALESCE(value_text,value_boolean,value_datetime) as attribute_value
FROM film_attribute_values
LEFT JOIN attributes ON film_attribute_values.attribute_id = attributes.id
LEFT JOIN attribute_types ON attributes.attribute_type_id = attribute_types.id
LEFT JOIN films ON film_attribute_values.film_id = films.id
WHERE attributes.is_service_attribute <> 1;

CREATE VIEW service_data AS
SELECT films.title as film,
       attributes.title as attribute,
       attribute_types.code as attribute_type,
       value_datetime as attribute_value,
       CASE
           WHEN value_datetime > DATE_ADD(CURDATE(), INTERVAL '20' DAY) THEN "Станет актуально через 20 дней"
           WHEN value_datetime <= NOW() THEN "Актуально на сегодня"
           END AS task_relevance
FROM film_attribute_values
         LEFT JOIN attributes ON film_attribute_values.attribute_id = attributes.id
         LEFT JOIN attribute_types ON attributes.attribute_type_id = attribute_types.id
         LEFT JOIN films ON film_attribute_values.film_id = films.id
WHERE attributes.is_service_attribute = 1;
