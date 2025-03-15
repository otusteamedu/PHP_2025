-- Представление данных для маркетинга
CREATE VIEW marketing_data_view AS
SELECT film.title                               as film,
       attribute.a_name                         as attribute_name,
       attribute_type.at_type                   as attribute_type,
       COALESCE(
               value.v_text,
               cast(value.v_date AS TEXT),
               cast(value.v_varchar AS TEXT),
               cast(value.v_numeric AS TEXT),
               cast(value.v_int AS TEXT),
               cast(value.v_bool AS TEXT),
               cast(value.v_small_int AS TEXT)) as attribute_value
FROM value
         LEFT JOIN attribute ON value.v_attribute_id = attribute.a_id
         LEFT JOIN attribute_type ON attribute.a_type_id = attribute_type.at_id
         LEFT JOIN film ON value.v_film_id = film.id
;



-- Представление сервисных данных
CREATE VIEW service_data_view AS
SELECT *
FROM (SELECT film.title     AS film_title,
             v1.v_date_time AS task_current_day,
             v2.v_date_time AS task_after_20_day
      FROM film
               LEFT JOIN value v1
                         ON film.id = v1.v_film_id AND v1.v_date_time::date = CURRENT_DATE::date
               LEFT JOIN value v2
                         ON film.id = v2.v_film_id AND v2.v_date_time::date = (CURRENT_DATE + INTERVAL '20 days')::date
               LEFT JOIN attribute
                         ON attribute.a_name = 'task_date' AND attribute.a_id = v1.v_attribute_id AND
                            attribute.a_id = v2.v_attribute_id) as data
WHERE task_current_day IS NOT NULL
   OR task_after_20_day IS NOT NULL
;






