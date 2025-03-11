CREATE VIEW service_view AS
SELECT m.name as movie_name,
       STRING_AGG(CASE WHEN (v.date_value = CURRENT_DATE) THEN a.name || ': ' || v.date_value END, '; ') AS tasks_today,
       STRING_AGG(CASE WHEN (v.date_value = (CURRENT_DATE + INTERVAL '20 DAYS')) THEN a.name || ': ' || v.date_value END, '; ') AS tasks_20_days
FROM attribute_values v
     JOIN movies m ON m.id = v.movie_id
     JOIN attributes a ON a.id = v.attribute_id
     JOIN attribute_types t ON t.id = a.type_id
WHERE t.id = 4 -- type = 'Служебные даты'
GROUP BY m.id;