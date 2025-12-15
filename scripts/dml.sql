insert into public.halls (id, title)
values (1, 'A'),
       (2, 'B'),
       (3, 'C');

insert into public.hall_seats (id, hall_id, number, row, col, seat_type)
values (1, 1, 'A1', 1, 1, 'seat'),
       (2, 1, 'A2', 1, 2, 'seat'),
       (3, 1, 'A3', 1, 3, 'seat'),
       (4, 2, 'B1', 1, 1, 'seat'),
       (5, 2, 'B2', 1, 2, 'seat'),
       (6, 3, 'C1', 1, 1, 'seat');

insert into public.movies (id, title)
values (1, 'A'),
       (2, 'B'),
       (3, 'C'),
       (4, 'ABC'),
       (5, 'AC');

insert into public.seances (id, begin_at, end_at, hall_id, movie_id, price)
values (1, '2025-11-25 12:00:00.000000', '2025-11-25 13:00:00.000000', 1, 1, 100),
       (2, '2025-11-25 12:00:00.000000', '2025-11-25 14:00:00.000000', 2, 2, 100),
       (3, '2025-11-25 15:00:00.000000', '2025-11-25 16:00:00.000000', 3, 3, 100),
       (4, '2025-11-25 18:00:00.000000', '2025-11-25 19:00:00.000000', 1, 4, 200),
       (5, '2025-11-25 19:00:00.000000', '2025-11-25 20:00:00.000000', 2, 4, 200),
       (6, '2025-11-25 20:00:00.000000', '2025-11-25 21:00:00.000000', 3, 4, 200),
       (7, '2025-11-25 21:00:00.000000', '2025-11-25 22:00:00.000000', 1, 5, 300),
       (8, '2025-11-25 22:00:00.000000', '2025-11-25 23:00:00.000000', 3, 5, 300);

insert into public.tickets (id, price, seance_id, hall_seat_id)
values (1, 100, 1, 1),
       (2, 200, 4, 2),
       (3, 300, 7, 3),
       (4, 100, 2, 4),
       (5, 200, 5, 5),
       (6, 100, 3, 6),
       (7, 200, 6, 6),
       (8, 300, 8, 6);

insert into public.movie_entity_attribute_types (id, type)
values (1, 'text'),
       (2, 'integer'),
       (3, 'double'),
       (4, 'boolean'),
       (5, 'datetime');

insert into public.movie_entity_attributes (id, movie_entity_attribute_type_id, attribute, mode)
values (1, 1, 'Рецензии', 1),
       (2, 4, 'Премия', 1),
       (3, 5, 'Мировая премьера', 1),
       (4, 5, 'Премьера в РФ', 1),
       (5, 5, 'Дата начала продажи билетов', 7),
       (6, 5, 'Дата запуска рекламы', 6),
       (7, 3, 'Рейтинг', 5);

insert into public.movie_entity_values (id, movie_entity_attribute_id, movie_id, value_text, value_integer,
                                        value_double, value_boolean, value_datetime)
values (1, 1, 1, 'Отличный фильм, сильная режиссура', null, null, null, null),
       (2, 1, 2, 'Затянуто, но красивая картинка', null, null, null, null),
       (3, 1, 3, 'Лучший фильм года', null, null, null, null),
       (4, 2, 1, null, null, null, true, null),
       (5, 2, 3, null, null, null, true, null),
       (6, 2, 4, null, null, null, false, null),
       (7, 3, 1, null, null, null, null, '2026-01-12'),
       (8, 3, 2, null, null, null, null, '2026-01-01'),
       (9, 3, 3, null, null, null, null, '2026-01-20'),
       (10, 4, 1, null, null, null, null, '2026-01-01'),
       (11, 4, 2, null, null, null, null, '2026-01-15'),
       (12, 4, 4, null, null, null, null, '2026-01-10'),
       (13, 5, 1, null, null, null, null, '2025-12-15'),
       (14, 5, 2, null, null, null, null, '2026-01-04'),
       (15, 5, 3, null, null, null, null, '2025-12-31'),
       (16, 6, 1, null, null, null, null, '2026-01-14'),
       (17, 6, 2, null, null, null, null, '2025-12-31'),
       (18, 6, 5, null, null, null, null, '2026-01-09'),
       (19, 7, 1, null, null, 5.7, null, null);
