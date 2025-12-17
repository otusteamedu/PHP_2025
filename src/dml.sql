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

insert into attribute (name, type) values
    ('Стоимость фильма в $', 'money'),
    ('Режиссёр', 'text'),
    ('Страна', 'text'),
    ('Жанр', 'text'),
    ('Длительность в минутах', 'integer'),
    ('Рейтинг', 'real'),
    ('Год выпуска', 'integer'),
    ('Дата премьеры', 'date'),
    ('Оскар', 'boolean');

-- Крестный отец
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Смирнов Мирослав Андреевич');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Австралия');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Романтика');
insert into value (movieId, attributeId, integerVal) values (1,  5, 90);
insert into value (movieId, attributeId, realVal) values (1,  6, '7.6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '2007');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2025-12-18');
insert into value (movieId, attributeId, booleanVal) values (1,  9, true);

-- Титаник
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Смирнов Мирослав Андреевич');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Австралия');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Комедия');
insert into value (movieId, attributeId, integerVal) values (1,  5, 120);
insert into value (movieId, attributeId, realVal) values (1,  6, '8.6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '2011');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2025-12-19');
insert into value (movieId, attributeId, booleanVal) values (1,  9, true);

-- Начало
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Фокин Фёдор Адамович');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Ирак');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Исторический');
insert into value (movieId, attributeId, integerVal) values (1,  5, 90);
insert into value (movieId, attributeId, realVal) values (1,  6, '6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '2022');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2025-12-20');
insert into value (movieId, attributeId, booleanVal) values (1,  9, true);

-- Матрица
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Фокин Фёдор Адамович');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Ирак');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Исторический');
insert into value (movieId, attributeId, integerVal) values (1,  5, 290);
insert into value (movieId, attributeId, realVal) values (1,  6, '4.6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '2023');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2026-01-07');
insert into value (movieId, attributeId, booleanVal) values (1,  9, true);

-- Интерстеллар
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Лапшина София Кирилловна');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Австралия');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Фантастика');
insert into value (movieId, attributeId, integerVal) values (1,  5, 190);
insert into value (movieId, attributeId, realVal) values (1,  6, '4.6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '1988');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2026-01-08');
insert into value (movieId, attributeId, booleanVal) values (1,  9, true);

-- Назад в будущее
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Крылова Валерия Ильинична');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Австралия');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Комедия');
insert into value (movieId, attributeId, integerVal) values (1,  5, 95);
insert into value (movieId, attributeId, realVal) values (1,  6, '4.6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '1990');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2026-01-09');
insert into value (movieId, attributeId, booleanVal) values (1,  9, false);

-- Побег из Шоушенка
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Смирнов Мирослав Андреевич');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Корея');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Фантастика');
insert into value (movieId, attributeId, integerVal) values (1,  5, 90);
insert into value (movieId, attributeId, realVal) values (1,  6, '4.2');
insert into value (movieId, attributeId, integerVal) values (1,  7, '2000');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2026-01-18');
insert into value (movieId, attributeId, booleanVal) values (1,  9, false);

-- Список Шиндлера
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Вишневская Алина Семёновна');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Корея');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Приключение');
insert into value (movieId, attributeId, integerVal) values (1,  5, 135);
insert into value (movieId, attributeId, realVal) values (1,  6, '8.6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '1999');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2025-12-28');
insert into value (movieId, attributeId, booleanVal) values (1,  9, false);

-- Криминальное чтиво
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Вишневская Алина Семёновна');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Лаос');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Комедия');
insert into value (movieId, attributeId, integerVal) values (1,  5, 120);
insert into value (movieId, attributeId, realVal) values (1,  6, '6.9');
insert into value (movieId, attributeId, integerVal) values (1,  7, '2003');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2025-12-19');
insert into value (movieId, attributeId, booleanVal) values (1,  9, false);

-- Трон: Наследие
insert into value (movieId, attributeId, moneyVal) values (1,  1, 1546456.43);
insert into value (movieId, attributeId, textVal) values (1,  2, 'Кузнецов Максим Максимович');
insert into value (movieId, attributeId, textVal) values (1,  3, 'Лаос');
insert into value (movieId, attributeId, textVal) values (1,  4, 'Приключение');
insert into value (movieId, attributeId, integerVal) values (1,  5, 120);
insert into value (movieId, attributeId, realVal) values (1,  6, '9.6');
insert into value (movieId, attributeId, integerVal) values (1,  7, '2024');
insert into value (movieId, attributeId, dateVal) values (1,  8, '2025-12-14');
insert into value (movieId, attributeId, booleanVal) values (1,  9, false);
