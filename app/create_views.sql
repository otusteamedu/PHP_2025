CREATE OR REPLACE VIEW actual_movie_view as
SELECT m.title AS movie_title,
       mav.timestamp_value,
       CASE
           WHEN mav.timestamp_value::DATE = CURRENT_DATE THEN 'Событие сегодня'
           ELSE 'Событие в ближайшем будущем'
END AS date_status
FROM movie m
         JOIN
     movie_attribute_value mav ON m.id = mav.movie_id
WHERE mav.timestamp_value BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL '20 days');


CREATE OR REPLACE VIEW marketing_info_view as
SELECT m.title AS movie_title,
       a.title AS attribute_title,
       at.title AS attribute_type,
       CASE
           at.type
           WHEN 'string' THEN mav.string_value
           WHEN 'text' THEN mav.text_value
           WHEN 'integer' THEN (mav.integer_value)::text
           WHEN 'float' THEN (mav.float_value)::text
           WHEN 'money' THEN (mav.money_value)::text
           WHEN 'boolean' THEN (mav.boolean_value)::text
           WHEN 'date' THEN (mav.date_value)::text
           WHEN 'datetime' THEN (mav.timestamp_value)::text
           ELSE ''::text
           END AS value
FROM movie m
    JOIN
    movie_attribute_value mav ON m.id = mav.movie_id
    JOIN attribute a
    on mav.attribute_id = a.id
    JOIN attribute_type at
    on at.attribute_id = a.id;