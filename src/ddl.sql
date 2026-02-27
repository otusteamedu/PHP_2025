-- drop table attribute, movie, value;

create table attribute_type
(
    id smallserial primary key,
    name character varying(20)
);

create table attribute
(
    id serial primary key,
    typeId smallint not null,
    name character varying(50) not null,
    foreign key (typeId) references attribute_type (id)
);

create table movie
(
    id bigserial primary key,
    name character varying(200) not null
);

create table value
(
    id bigserial primary key,
    name character varying(50),
    movieId bigint not null,
    attributeId integer not null,
    integerVal integer default null,
    realVal real default null,
    textVal text default null,
    timestampVal timestamp default null,
    dateVal date default null,
    booleanVal boolean default null,
    moneyVal money default null,
    foreign key (movieId) references movie (id),
    foreign key (attributeId) references attribute (id)
);