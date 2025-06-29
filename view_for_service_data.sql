CREATE VIEW service_data AS
SELECT f.title AS film, 
       v.value_date AS task_date
FROM films f
JOIN values v ON f.film_id = v.film_id
WHERE v.attribute_id IN (SELECT attribute_id FROM attributes WHERE attribute_name = 'ticket_sale_start');
