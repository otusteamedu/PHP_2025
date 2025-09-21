create view marketing as 
    select movie.name, attribute_type.name as attribute_type_name, attribute.name as attribute_name, 
    coalesce(
        cast(value.string_value as text),
        cast(value.int_value as text),
        cast(value.float_value as text),
        cast(value.date_value as text),
        cast(value.bool_value as text),
        cast(value.decimal_value as text)
    ) as attribute_value
    from movie
    left join value on movie.movie_id = value.entity_id
    left join attribute on value.attribute_id = attribute.id
    left join attribute_type on attribute.attribute_type_id = attribute_type.id;