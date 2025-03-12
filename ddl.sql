-- Типы атрибутов
create table attribute_types
(
    id   serial primary key,
    name varchar(255) unique
);

-- Фильмы
create table films
(
    id   serial primary key,
    name varchar(255) unique
);

-- Атрибуты
create table attributes
(
    id      serial primary key,
    type_id int references attribute_types (id),
    name    varchar(255) unique
);

-- Значения атрибутов
create table attribute_values
(
    id           serial primary key,
    attribute_id int references attributes (id),
    film_id      int references films (id),
    value_text   text null,
    value_bool   boolean null,
    value_number DECIMAL(10, 2) null,
    value_date   timestamp null
);

create index index_attribute_values_attribute_id on attribute_values (attribute_id);
create index index_attribute_values_film_id on attribute_values (film_id);

-- Представление служебных данных
create view marketing_view as
select F.name   as film_name,
       A_T.name as attribute_type_name,
       A.name   as attribute_name,
       COALESCE(
               A_V.value_text,
               cast(A_V.value_date as varchar),
               cast(A_V.value_number as varchar),
               cast(A_V.value_bool as varchar)
       ) as value
from films F
join attribute_values A_V on F.id = A_V.film_id
join attributes A on A.id = A_V.attribute_id
join attribute_types A_T on A.type_id = A_T.id;

-- Представление данных для маркетинга
create view service_view as
select *
from (select F.name          as film_name,
             A_V.value_text  as task_current_day,
             A_V2.value_text as task_after_20_day
      from films F
               left join attribute_values A_V
                         on F.id = A_V.film_id and A_V.value_date = current_date
               left join attribute_values A_V2
                         on F.id = A_V2.film_id and A_V2.value_date = (CURRENT_DATE + interval '20 days')
               left join attributes A
                         on A.name = 'Дата актуальной задачи' and A.id = A_V.attribute_id and A.id = A_V2.attribute_id)
where task_current_day is not null
   or task_after_20_day is not null;