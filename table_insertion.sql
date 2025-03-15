INSERT INTO film (title)
VALUES ('Бегущий по лезвию'),
       ('Интерстеллар'),
       ('Начало'),
       ('Бриллиантовая рука'),
       ('Птица')
;

INSERT INTO attribute_type (at_type)
VALUES ('text'),
       ('date'),
       ('date_time'),
       ('varchar'),
       ('numeric'),
       ('int'),
       ('boolean'),
       ('small_int')
;
CREATE INDEX idx_attribute_type_type ON attribute_type (at_type);


INSERT INTO attribute (a_name, a_type_id)
VALUES ('slogan', (SELECT attribute_type.at_id FROM attribute_type WHERE attribute_type.at_type = 'varchar')),
       ('price', (SELECT attribute_type.at_id FROM attribute_type WHERE attribute_type.at_type = 'numeric')),
       ('started_at', (SELECT attribute_type.at_id FROM attribute_type WHERE attribute_type.at_type = 'date')),
       ('task_date', (SELECT attribute_type.at_id FROM attribute_type WHERE attribute_type.at_type = 'date_time')),
       ('description', (SELECT attribute_type.at_id FROM attribute_type WHERE attribute_type.at_type = 'text')),
       ('age_limit', (SELECT attribute_type.at_id FROM attribute_type WHERE attribute_type.at_type = 'small_int')),
       ('duration_in_min',
        (SELECT attribute_type.at_id FROM attribute_type WHERE attribute_type.at_type = 'small_int')),
       ('approved', (SELECT attribute_type.at_id FROM attribute_type WHERE attribute_type.at_type = 'boolean'))
;

INSERT INTO value (v_film_id, v_attribute_id, v_varchar)
VALUES ((SELECT film.id FROM film WHERE film.title = 'Бегущий по лезвию'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'slogan')),
        'Репликанты противятся истреблению и периодически поднимают восстания против людей'),
       ((SELECT film.id FROM film WHERE film.title = 'Интерстеллар'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'slogan')),
        'В 2067 году неурожай и пыльные бури угрожают выживанию человечества'),
       ((SELECT film.id FROM film WHERE film.title = 'Начало'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'slogan')),
        'Начало процесса или организации')
;

INSERT INTO value (v_film_id, v_attribute_id, v_date)
VALUES ((SELECT film.id FROM film WHERE film.title = 'Бегущий по лезвию'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'started_at')),
        '1982-06-22'),
       ((SELECT film.id FROM film WHERE film.title = 'Интерстеллар'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'started_at')),
        '2014-06-22'),
       ((SELECT film.id FROM film WHERE film.title = 'Начало'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'started_at')),
        '2010-06-22'),
       ((SELECT film.id FROM film WHERE film.title = 'Бриллиантовая рука'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'started_at')),
        '1969-06-22')
;

INSERT INTO value (v_film_id, v_attribute_id, v_small_int)
VALUES ((SELECT film.id FROM film WHERE film.title = 'Бегущий по лезвию'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'age_limit')),
        18),
       ((SELECT film.id FROM film WHERE film.title = 'Интерстеллар'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'age_limit')),
        16),
       ((SELECT film.id FROM film WHERE film.title = 'Начало'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'age_limit')),
        16),
       ((SELECT film.id FROM film WHERE film.title = 'Бриллиантовая рука'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'age_limit')),
        6)
;

INSERT INTO value (v_film_id, v_attribute_id, v_small_int)
VALUES ((SELECT film.id FROM film WHERE film.title = 'Бегущий по лезвию'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'duration_in_min')),
        117),
       ((SELECT film.id FROM film WHERE film.title = 'Интерстеллар'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'duration_in_min')),
        169),
       ((SELECT film.id FROM film WHERE film.title = 'Начало'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'duration_in_min')),
        148),
       ((SELECT film.id FROM film WHERE film.title = 'Бриллиантовая рука'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'duration_in_min')),
        100)
;
INSERT INTO value (v_film_id, v_attribute_id, v_bool)
VALUES ((SELECT film.id FROM film WHERE film.title = 'Бегущий по лезвию'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'approved')),
        true),
       ((SELECT film.id FROM film WHERE film.title = 'Интерстеллар'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'approved')),
        true),
       ((SELECT film.id FROM film WHERE film.title = 'Начало'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'approved')),
        false),
       ((SELECT film.id FROM film WHERE film.title = 'Бриллиантовая рука'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'approved')),
        false)
;
INSERT INTO value (v_film_id, v_attribute_id, v_date_time)
VALUES ((SELECT film.id FROM film WHERE film.title = 'Бегущий по лезвию'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'task_date')),
        '2025-03-12 09:00:00'),
       ((SELECT film.id FROM film WHERE film.title = 'Интерстеллар'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'task_date')),
        '2025-03-31 09:00:00'),
       ((SELECT film.id FROM film WHERE film.title = 'Начало'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'task_date')),
        current_timestamp),
       ((SELECT film.id FROM film WHERE film.title = 'Бриллиантовая рука'),
        ((SELECT attribute.a_id FROM attribute WHERE attribute.a_name = 'task_date')),
        '2025-04-04 09:00:00')
;