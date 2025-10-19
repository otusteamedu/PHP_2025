-- drop database otus_cinema_eav;
create database otus_cinema_eav;

create table otus_cinema_eav.movie (
    id serial primary key,
    name varchar(255) not null
);

create index idx_movie_name on otus_cinema_eav.movie(name);

create table otus_cinema_eav.attributeType (
    id serial primary key,
    type varchar(50) not null,
    name varchar(255) not null
);

create table otus_cinema_eav.attribute (
    id serial primary key,
    name varchar(255) not null,
    attributeTypeId bigint unsigned not null,
    foreign key (attributeTypeId) references attributeType(id)
);

create index idx_attribute_name on otus_cinema_eav.attribute(name);

create table otus_cinema_eav.attributeValue (
    id serial primary key,
    movieId bigint unsigned not null,
    attributeId bigint unsigned not null,
    stringValue varchar(255) default null,
    textValue text default null,
    intValue int default null,
    floatValue real default null,
    boolValue bool default null,
    datetimeValue datetime default null,
    dateValue date default null,
    foreign key (movieId) references movie(id),
    foreign key (attributeId) references attribute(id)
);

create index idx_movie_attribute on otus_cinema_eav.attributeValue(movieId, attributeId);
