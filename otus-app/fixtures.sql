insert into otus_cinema_eav.movie (name)
values ('movie1'),
       ('movie2'),
       ('movie3');

insert into otus_cinema_eav.attributeType (id, type, name)
values (1, 'text', 'рецензии'),
       (2, 'bool', 'премии'),
       (3, 'date', 'важные даты'),
       (4, 'datetime', 'начало сеансов'),
       (5, 'string', 'жанры'),
       (6, 'int', 'возрастные рейтинги'),
       (7, 'float', 'рейтинги'),
       (8, 'date', 'служебные даты');

insert into otus_cinema_eav.attribute (id, name, attributeTypeId)
values (1, 'рецензии критиков', 1),
       (2, 'оскар', 2),
       (10, 'ника', 2),
       (3, 'дата выхода', 3),
       (4, 'начало продажи билетов', 8),
       (9, 'начало запуска рекламы', 8),
       (5, 'дата первого показа', 4),
       (6, 'жанры в Канаде', 5),
       (7, 'возрастной рейтинг в Канаде', 6),
       (8, 'рейтинг кинопоиска', 7),
       (11, 'рейтинг imdb', 7)
;

insert into otus_cinema_eav.attributeValue (movieId, attributeId, stringValue, textValue, intValue, floatValue, boolValue, datetimeValue, dateValue)
values (1, 1, null, 'text_for_movie1_text_for_movie1_text_for_movie1_text_for_movie1_text_for_movie1_text_for_movie1_text_for_movie1_text_for_movie1_text_for_movie1_text_for_movie1_', null, null, null, null, null),
       (1, 2, null, null, null, null, 1, null, null),
       (1, 3, null, null, null, null, null, null, '2025-10-10'),
       (1, 4, null, null, null, null, null, null, '2025-10-01'),
       (1, 5, null, null, null, null, null, '2025-10-10 12:00:00', null),
       (1, 6, 'комедия', null, null, null, null, null, null),
       (1, 7, null, null, 18, null, null, null, null),
       (1, 8, null, null, null, 4.21, null, null, null),
       (1, 9, null, null, null, null, null, null, '2025-10-02'),
       (1, 10, null, null, null, null, 0, null, null),
       (1, 11, null, null, null, 4.27, null, null, null),

       (2, 2, null, null, null, null, 0, null, null),
       (2, 3, null, null, null, null, null, null, '2025-11-11'),
       (2, 4, null, null, null, null, null, null, '2025-11-01'),
       (2, 5, null, null, null, null, null, '2025-11-11 14:00:00', null),
       (2, 6, 'ужасы', null, null, null, null, null, null),
       (2, 7, null, null, 16, null, null, null, null),
       (2, 9, null, null, null, null, null, null, '2025-11-02'),
       (2, 10, null, null, null, null, 0, null, null),

       (3, 1, null, '_text_for_movie3_text_for_movie3_text_for_movie3_text_for_movie3_text_for_movie3_text_for_movie3_text_for_movie3_text_for_movie3_text_for_movie3_text_for_movie3', null, null, null, null, null),
       (3, 2, null, null, null, null, 1, null, null),
       (3, 3, null, null, null, null, null, null, '2025-09-10'),
       (3, 4, null, null, null, null, null, null, '2025-09-01'),
       (3, 5, null, null, null, null, null, '2025-09-10 09:00:00', null),
       (3, 6, 'ужасы', null, null, null, null, null, null),
       (3, 6, 'боевик', null, null, null, null, null, null),
       (3, 7, null, null, 21, null, null, null, null),
       (3, 8, null, null, null, 4.87, null, null, null),
       (3, 9, null, null, null, null, null, null, '2025-09-02'),
       (3, 10, null, null, null, null, 1, null, null),
       (3, 11, null, null, null, 4.99, null, null, null)
;
