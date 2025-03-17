/* Выводим список действий актуальных на грядущие даты */

SELECT 
	films.name film_name,
    films_attrs_values.date attr_value_date,
    films_attrs_types.type attr_type
FROM `films` 
LEFT JOIN films_attrs ON films_attrs.film_id = films.id
LEFT JOIN films_attrs_values ON films_attrs_values.id = films_attrs.attr_id
LEFT JOIN films_attrs_types ON films_attrs_types.id = films_attrs_values.attr_type_id
WHERE films_attrs_values.date >= NOW()
ORDER BY films.id;