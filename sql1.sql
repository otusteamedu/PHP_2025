/* Выбор всех фильмов на сегодня */

SELECT 

    res.film_name film_name, 
    res.date_start date_start, 
    res.date_end date_end
    
    FROM (

        SELECT 

        end_date.film_name film_name, 
        start_date.start_date date_start, 
        end_date.end_date date_end

        FROM (

            SELECT 
                films.name film_name,
                films_attrs_values.date end_date
            FROM `films` 
            LEFT JOIN films_attrs ON films_attrs.film_id = films.id
            LEFT JOIN films_attrs_values ON films_attrs_values.id = films_attrs.attr_id
            LEFT JOIN films_attrs_types ON films_attrs_types.id = films_attrs_values.attr_type_id 
            WHERE films_attrs_types.type = 'Окончание показа' 

        ) end_date  

        LEFT JOIN (

            SELECT start_date.film_name, start_date.start_date 

            FROM (

                SELECT 
                    films.name film_name,
                    films_attrs_values.date start_date
                FROM `films` 
                LEFT JOIN films_attrs ON films_attrs.film_id = films.id
                LEFT JOIN films_attrs_values ON films_attrs_values.id = films_attrs.attr_id
                LEFT JOIN films_attrs_types ON films_attrs_types.id = films_attrs_values.attr_type_id 
                WHERE films_attrs_types.type = 'Премьера' 

            ) start_date

        ) start_date ON start_date.film_name = end_date.film_name

    ) res 
    
WHERE NOW() BETWEEN res.date_start AND res.date_end;