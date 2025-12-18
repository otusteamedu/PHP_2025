insert into movie (name) values
    ('Крестный отец'),
    ('Титаник'),
    ('Начало'),
    ('Матрица'),
    ('Интерстеллар'),
    ('Назад в будущее'),
    ('Побег из Шоушенка'),
    ('Список Шиндлера'),
    ('Криминальное чтиво'),
    ('Трон: Наследие');

insert into attribute_type (name) values
    ('money'),
    ('text'),
    ('integer'),
    ('real'),
    ('date'),
    ('boolean');

insert into attribute (name, typeId) values
    ('Стоимость фильма в $', 1),
    ('Режиссёр', 2),
    ('Страна', 2),
    ('Жанр', 2),
    ('Длительность в минутах', 3),
    ('Рейтинг', 4),
    ('Год выпуска', 3),
    ('Дата премьеры', 5),
    ('Оскар', 6);

-- Крестный отец
insert into value (movieId, attributeId, moneyVal) values (1,  1, 7657845.53);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Смирнов Мирослав Андреевич');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Австралия');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Романтика');
insert into value (movieId, attributeId, integerVal) values (1,  5, 90);
insert into value (movieId, attributeId, realVal) values (1,  6, '7.6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '2007');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2025-12-18');
insert into value (movieId, attributeId, booleanVal) values (1,  9, true);

-- Титаник
insert into value (movieId, attributeId, moneyVal) values (2,  1, 3453426.43);
insert into value (movieId, attributeId, textVal) values (2,  2, 'Смирнов Мирослав Андреевич');
insert into value (movieId, attributeId, textVal) values (2,  3, 'Австралия');
insert into value (movieId, attributeId, textVal) values (2,  4, 'Комедия');
insert into value (movieId, attributeId, integerVal) values (2,  5, 120);
insert into value (movieId, attributeId, realVal) values (2,  6, '8.6');
insert into value (movieId, attributeId, integerVal) values (2,  7, '2011');
insert into value (movieId, attributeId, dateVal) values (2,  8, '2025-12-19');
insert into value (movieId, attributeId, booleanVal) values (2,  9, true);

-- Начало
insert into value (movieId, attributeId, moneyVal) values (3,  1, 4798432.35);
insert into value (movieId, attributeId, textVal) values (3,  2, 'Фокин Фёдор Адамович');
insert into value (movieId, attributeId, textVal) values (3,  3, 'Ирак');
insert into value (movieId, attributeId, textVal) values (3,  4, 'Исторический');
insert into value (movieId, attributeId, integerVal) values (3,  5, 90);
insert into value (movieId, attributeId, realVal) values (3,  6, '6');
insert into value (movieId, attributeId, integerVal) values (3,  7, '2022');
insert into value (movieId, attributeId, dateVal) values (3,  8, '2025-12-20');
insert into value (movieId, attributeId, booleanVal) values (3,  9, true);

-- Матрица
insert into value (movieId, attributeId, moneyVal) values (4,  1, 7854889.40);
insert into value (movieId, attributeId, textVal) values (4,  2, 'Фокин Фёдор Адамович');
insert into value (movieId, attributeId, textVal) values (4,  3, 'Ирак');
insert into value (movieId, attributeId, textVal) values (4,  4, 'Исторический');
insert into value (movieId, attributeId, integerVal) values (4,  5, 290);
insert into value (movieId, attributeId, realVal) values (4,  6, '4.6');
insert into value (movieId, attributeId, integerVal) values (4,  7, '2023');
insert into value (movieId, attributeId, dateVal) values (4,  8, '2026-01-07');
insert into value (movieId, attributeId, booleanVal) values (4,  9, true);

-- Интерстеллар
insert into value (movieId, attributeId, moneyVal) values (5,  1, 78546456.46);
insert into value (movieId, attributeId, textVal) values (5,  2, 'Лапшина София Кирилловна');
insert into value (movieId, attributeId, textVal) values (5,  3, 'Австралия');
insert into value (movieId, attributeId, textVal) values (5,  4, 'Фантастика');
insert into value (movieId, attributeId, integerVal) values (5,  5, 190);
insert into value (movieId, attributeId, realVal) values (5,  6, '4.6');
insert into value (movieId, attributeId, integerVal) values (5,  7, '1988');
insert into value (movieId, attributeId, dateVal) values (5,  8, '2026-01-08');
insert into value (movieId, attributeId, booleanVal) values (5,  9, true);

-- Назад в будущее
insert into value (movieId, attributeId, moneyVal) values (6,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (6,  2, 'Крылова Валерия Ильинична');
insert into value (movieId, attributeId, textVal) values (6,  3, 'Австралия');
insert into value (movieId, attributeId, textVal) values (6,  4, 'Комедия');
insert into value (movieId, attributeId, integerVal) values (6,  5, 95);
insert into value (movieId, attributeId, realVal) values (6,  6, '4.6');
insert into value (movieId, attributeId, integerVal) values (6,  7, '1990');
insert into value (movieId, attributeId, dateVal) values (6,  8, '2026-01-09');
insert into value (movieId, attributeId, booleanVal) values (6,  9, false);

-- Побег из Шоушенка
insert into value (movieId, attributeId, moneyVal) values (7,  1, 154213556.43);
insert into value (movieId, attributeId, textVal) values (7,  2, 'Смирнов Мирослав Андреевич');
insert into value (movieId, attributeId, textVal) values (7,  3, 'Корея');
insert into value (movieId, attributeId, textVal) values (7,  4, 'Фантастика');
insert into value (movieId, attributeId, integerVal) values (7,  5, 90);
insert into value (movieId, attributeId, realVal) values (7,  6, '4.2');
insert into value (movieId, attributeId, integerVal) values (7,  7, '2000');
insert into value (movieId, attributeId, dateVal) values (7,  8, '2026-01-18');
insert into value (movieId, attributeId, booleanVal) values (7,  9, false);

-- Список Шиндлера
insert into value (movieId, attributeId, moneyVal) values (8,  1, 1546453456.43);
insert into value (movieId, attributeId, textVal) values (8,  2, 'Вишневская Алина Семёновна');
insert into value (movieId, attributeId, textVal) values (8,  3, 'Корея');
insert into value (movieId, attributeId, textVal) values (8,  4, 'Приключение');
insert into value (movieId, attributeId, integerVal) values (8,  5, 135);
insert into value (movieId, attributeId, realVal) values (8,  6, '8.6');
insert into value (movieId, attributeId, integerVal) values (8,  7, '1999');
insert into value (movieId, attributeId, dateVal) values (8,  8, '2025-12-28');
insert into value (movieId, attributeId, booleanVal) values (8,  9, false);

-- Криминальное чтиво
insert into value (movieId, attributeId, moneyVal) values (19,  1, 1354546456.77);
insert into value (movieId, attributeId, textVal) values (19,  2, 'Вишневская Алина Семёновна');
insert into value (movieId, attributeId, textVal) values (19,  3, 'Лаос');
insert into value (movieId, attributeId, textVal) values (19,  4, 'Комедия');
insert into value (movieId, attributeId, integerVal) values (19,  5, 120);
insert into value (movieId, attributeId, realVal) values (19,  6, '6.9');
insert into value (movieId, attributeId, integerVal) values (19,  7, '2003');
insert into value (movieId, attributeId, dateVal) values (19,  8, '2025-12-19');
insert into value (movieId, attributeId, booleanVal) values (19,  9, false);

-- Трон: Наследие
insert into value (movieId, attributeId, moneyVal) values (10,  1, 100876456.73);
insert into value (movieId, attributeId, textVal) values (10,  2, 'Кузнецов Максим Максимович');
insert into value (movieId, attributeId, textVal) values (10,  3, 'Лаос');
insert into value (movieId, attributeId, textVal) values (10,  4, 'Приключение');
insert into value (movieId, attributeId, integerVal) values (10,  5, 120);
insert into value (movieId, attributeId, realVal) values (10,  6, '9.6');
insert into value (movieId, attributeId, integerVal) values (10,  7, '2024');
insert into value (movieId, attributeId, dateVal) values (10,  8, '2025-12-14');
insert into value (movieId, attributeId, booleanVal) values (10,  9, false);
