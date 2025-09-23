create table movie(
    movie_id serial primary key,
    name varchar(255) not null,
    description text
);


create table attribute_type(
    id serial primary key,
    name varchar(255) not null
);

create table attribute(
    id serial primary key,
    attribute_type_id int references attribute_type(id),
    name varchar(255) not null
);

create table value(
    id serial primary key,
    entity_id int references movie(movie_id),
    attribute_id int references attribute(id),
    string_value text,
    int_value bigint,
    float_value real,
    date_value timestamp,
    bool_value boolean
);


create index idx_movie_id on value(entity_id);
create index idx_attribute_id on value(attribute_id);
create index idx_attribute_type_id on attribute(attribute_type_id);