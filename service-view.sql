create view service_view as
    with date_values as (
        select movie.movie_id as movie_id, attribute_type.name as attribute_type_name, attribute.name as attribute_name, value.date_value as todo_date 
            from movie
        left join value on movie.movie_id = value.entity_id
        left join attribute on value.attribute_id = attribute.id
        left join attribute_type on attribute.attribute_type_id = attribute_type.id
            where attribute_type.name = 'Служебная дата'
    ),
    today_tasks as (
        select date_values.movie_id as today_table_movie_id, string_agg(date_values.attribute_name, ', ') as today_tasks 
        from date_values
        where todo_date <= current_date
        group by date_values.movie_id
    ),
    future_tasks as (
        select date_values.movie_id as future_table_movie_id, string_agg(date_values.attribute_name, ', ') as future_tasks 
        from date_values
        where todo_date >= (current_date + 20)
        group by date_values.movie_id
    )
    select movie.name as movi_name, today_tasks.today_tasks as actual_today, future_tasks.future_tasks as actual_in_20_days from movie 
    join today_tasks on movie.movie_id = today_tasks.today_table_movie_id
    join future_tasks on movie.movie_id = future_tasks.future_table_movie_id;