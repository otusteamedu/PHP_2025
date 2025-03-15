CREATE VIEW view_service AS
SELECT f.name,
       concat(
               ' Актуально сегодня: ',
               string_agg(
                       CASE
                           WHEN date (v.value_date) = CURRENT_DATE THEN concat(a."name", ' ', v.value_date)
                           END,
                       ', '
               )
       ) AS task_today,
       CONCAT(
               ' Актуально через 20 дней: ',
               string_agg(
                       CASE
                           WHEN date (v.value_date) >= date (CURRENT_DATE + INTERVAL '20 days') THEN concat(a."name", ' ', v.value_date)
                           END,
                       ', '
               )
       ) AS task_after_20_days
FROM films AS f
         JOIN attribute_values AS v ON f.id = v.film_id
         JOIN attributes AS a ON a.id = v.attibute_id
         JOIN attribute_types AS at ON at.id = a.type_id
    where at.id = 4 and (date(v.value_date) = CURRENT_DATE OR date(v.value_date) >= date(CURRENT_DATE + INTERVAL '20 days'))
    GROUP BY f."name"
