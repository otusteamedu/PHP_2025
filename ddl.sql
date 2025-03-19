CREATE TABLE movies
(
    id    serial primary key,
    title text not null
);

CREATE TABLE attribute_types
(
    id        serial primary key,
    name      text unique not null,
    data_type text not null check (data_type in ('text', 'boolean', 'date', 'float', 'integer'))
);

CREATE TABLE attributes
(
    id      serial primary key,
    name    text not null,
    type_id int references attribute_types (id) ON DELETE CASCADE
);

CREATE TABLE movie_attributes
(
    movie_id      int references movies (id) ON DELETE CASCADE,
    attribute_id  int references attributes (id) ON DELETE CASCADE,
    value_text    text,
    value_boolean boolean,
    value_date    date,
    value_float   double precision,
    value_integer int,
    primary key (movie_id, attribute_id)
);






