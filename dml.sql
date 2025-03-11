-- Movies
INSERT INTO movies (name)
VALUES ('Зеленая миля'),
       ('Форрест Гамп');

-- Attribute types
INSERT INTO attribute_types (name, data_type)
VALUES ('Рецензии', 'text'),
       ('Премии', 'boolean'),
       ('Важные даты', 'date'),
       ('Служебные даты', 'date'),
       ('Рейтинги', 'numeric'),
       ('Числовые характеристики', 'int'),
       ('Описания', 'text');

-- Attributes
INSERT INTO attributes (type_id, name)
VALUES (1, 'Рецензия критиков'),
       (1, 'Отзыв неизвестной киноакадемии'),
       (2, 'Оскар'),
       (2, 'Золотой глобус'),
       (2, 'Ника'),
       (3, 'Мировая премьера'),
       (3, 'Премьера в РФ'),
       (4, 'Дата начала продажи билетов'),
       (4, 'Когда запускать рекламу на ТВ'),
       (5, 'Рейтинг'),
       (5, 'Рейтинг IMDb'),
       (6, 'Продолжительность, мин.'),
       (6, 'Возрастное ограничение'),
       (6, 'Год производства'),
       (6, 'Бюджет'),
       (6, 'Сборы в мире'),
       (7, 'Описание');

-- Attribute values for movie "Зеленая миля"
INSERT INTO attribute_values (movie_id, attribute_id, int_value, numeric_value, boolean_value, date_value, text_value)
VALUES (1, 1, NULL, NULL, NULL, NULL,
        'Кажется удивительным, что два величайших шедевра кино переносят нас в самое мрачное место на свете - тюрьма, камера смертников, последнее место их пребывания, прежде чем они отправятся на электрический стул. Казалось бы, как можно рассказать жизнеутверждающие истории, полные надежды, силы духа и веры в лучшее - но как и в оригинале у Стивена Кинга, так и в фильме Фрэнка Дарабонта, сюжет наполнен какой-то невероятной энергией. История захватывает, воодушевляет, разбивает сердце, оставляет после просмотра много пищи для размышления. '),
       (1, 2, NULL, NULL, NULL, NULL,
        'Актерская игра в «Зеленой миле» восхищает и оставляет глубокое впечатление. Том Хэнкс великолепно сыграл роль охранника, создав тонкий и внушительный образ. Майкл Кларк Дункан воплощает Джона Коффи с удивительной силой и нежностью. Весь актерский состав работает в совершенстве. Даря зрителю невероятную эмоциональность и захватывающие моменты.'),
       (1, 3, NULL, NULL, true, NULL, NULL),
       (1, 4, NULL, NULL, true, NULL, NULL),
       (1, 5, NULL, NULL, false, NULL, NULL),
       (1, 6, NULL, NULL, NULL, '1999-12-06', NULL),
       (1, 7, NULL, NULL, NULL, '2000-04-18', NULL),
       (1, 8, NULL, NULL, NULL, '2025-04-01', NULL),
       (1, 9, NULL, NULL, NULL, '2025-03-12', NULL),
       (1, 10, NULL, 9.1, NULL, NULL, NULL),
       (1, 11, NULL, 8.6, NULL, NULL, NULL),
       (1, 12, 189, NULL, NULL, NULL, NULL),
       (1, 13, 18, NULL, NULL, NULL, NULL),
       (1, 14, 1999, NULL, NULL, NULL, NULL),
       (1, 15, 60000000, NULL, NULL, NULL, NULL),
       (1, 16, 286801374, NULL, NULL, NULL, NULL),
       (1, 17, NULL, NULL, NULL, NULL,
        'Пол Эджкомб — начальник блока смертников в тюрьме «Холодная гора», каждый из узников которого однажды проходит «зеленую милю» по пути к месту казни. Пол повидал много заключённых и надзирателей за время работы. Однако гигант Джон Коффи, обвинённый в страшном преступлении, стал одним из самых необычных обитателей блока.');

-- Attribute values for movie "Форрест Гамп"
INSERT INTO attribute_values (movie_id, attribute_id, int_value, numeric_value, boolean_value, date_value, text_value)
VALUES (2, 1, NULL, NULL, NULL, NULL,
        '«Форрест Гамп» прежде всего очень душевный фильм. В нём есть некая доброта и искренность. В первую очередь благодаря главному герою. Это проявляется за счёт того, как Форрест Гамп относится к другим людям. Как он относится к жизни. Он не самый умный человек на Земле, но во многих ситуациях понимает и чувствует жизнь лучше, чем другие люди. Он умеет любить и быть другом. А это одни из самых главных качеств человека.'),
       (2, 2, NULL, NULL, NULL, NULL,
        'Сценарий в фильме очень удачный. Нам рассказывают просто поразительную историю о человеке, который за свою жизнь повидал много и был где-то свидетелем, ну а где-то участником тех событий, которые коснулись его страны. Просто удивительно, что всё это произошло в жизни одного человека. Конечно это всего лишь фильм, но невозможно не прийти в восторг от увиденного.'),
       (2, 3, NULL, NULL, true, NULL, NULL),
       (2, 4, NULL, NULL, true, NULL, NULL),
       (2, 5, NULL, NULL, false, NULL, NULL),
       (2, 6, NULL, NULL, NULL, '1994-06-23', NULL),
       (2, 7, NULL, NULL, NULL, '2020-02-13', NULL),
       (2, 8, NULL, NULL, NULL, '2025-04-01', NULL),
       (2, 9, NULL, NULL, NULL, '2025-03-12', NULL),
       (2, 10, NULL, 8.9, NULL, NULL, NULL),
       (2, 11, NULL, 8.8, NULL, NULL, NULL),
       (2, 12, 142, NULL, NULL, NULL, NULL),
       (2, 13, 18, NULL, NULL, NULL, NULL),
       (2, 14, 1994, NULL, NULL, NULL, NULL),
       (2, 15, 55000000, NULL, NULL, NULL, NULL),
       (2, 16, 677387716, NULL, NULL, NULL, NULL),
       (2, 17, NULL, NULL, NULL, NULL,
        'Сидя на автобусной остановке, Форрест Гамп — не очень умный, но добрый и открытый парень — рассказывает случайным встречным историю своей необыкновенной жизни. С самого малолетства парень страдал от заболевания ног, соседские мальчишки дразнили его, но в один прекрасный день Форрест открыл в себе невероятные способности к бегу. Подруга детства Дженни всегда его поддерживала и защищала, но вскоре дороги их разошлись.');
