-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: mysql
-- Время создания: Мар 17 2025 г., 18:30
-- Версия сервера: 9.1.0
-- Версия PHP: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cinemahall`
--

-- --------------------------------------------------------

--
-- Структура таблицы `films`
--

CREATE TABLE `films` (
  `id` int NOT NULL,
  `name` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `films`
--

INSERT INTO `films` (`id`, `name`) VALUES
(1, 'Фильм 1'),
(2, 'Фильм 2'),
(3, 'Фильм 3');

-- --------------------------------------------------------

--
-- Структура таблицы `films_attrs`
--

CREATE TABLE `films_attrs` (
  `id` int NOT NULL,
  `film_id` int DEFAULT NULL,
  `attr_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `films_attrs`
--

INSERT INTO `films_attrs` (`id`, `film_id`, `attr_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 8),
(4, 2, 6),
(5, 2, 3),
(6, 2, 9),
(7, 3, 12),
(8, 3, 4),
(9, 3, 10),
(10, 1, 13),
(11, 2, 14),
(12, 3, 15),
(13, 1, 16),
(14, 1, 17),
(15, 2, 18),
(16, 3, 19),
(17, 1, 20);

-- --------------------------------------------------------

--
-- Структура таблицы `films_attrs_types`
--

CREATE TABLE `films_attrs_types` (
  `id` int NOT NULL,
  `type` tinytext,
  `data_type` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `films_attrs_types`
--

INSERT INTO `films_attrs_types` (`id`, `type`, `data_type`) VALUES
(1, 'Рецензия', 'text'),
(2, 'Премьера', 'date'),
(3, 'Предпоказ', 'date'),
(4, 'Начало продаж', 'date'),
(5, 'Оскар', 'boolean'),
(6, 'Ника', 'boolean'),
(7, 'Золотой граммофон', 'boolean'),
(8, 'Сборы в мире', 'float'),
(9, 'Окончание показа', 'date');

-- --------------------------------------------------------

--
-- Структура таблицы `films_attrs_values`
--

CREATE TABLE `films_attrs_values` (
  `id` int NOT NULL,
  `attr_type_id` int DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `date` date DEFAULT NULL,
  `boolean` tinyint(1) DEFAULT NULL,
  `float` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `films_attrs_values`
--

INSERT INTO `films_attrs_values` (`id`, `attr_type_id`, `text`, `date`, `boolean`, `float`) VALUES
(1, 3, NULL, '2025-03-28', NULL, NULL),
(2, 1, 'Рецензия на фильм 1', NULL, NULL, NULL),
(3, 1, 'Рецензия на фильм 2', NULL, NULL, NULL),
(4, 1, 'Рецензия на фильм 3', NULL, NULL, NULL),
(5, 2, NULL, '2025-03-14', NULL, NULL),
(6, 2, NULL, '2025-03-15', NULL, NULL),
(7, 2, NULL, '2025-03-16', NULL, NULL),
(8, 4, NULL, '2025-03-27', NULL, NULL),
(9, 4, NULL, '2025-03-30', NULL, NULL),
(10, 4, NULL, '2025-03-25', NULL, NULL),
(11, 2, NULL, '2025-03-19', NULL, NULL),
(12, 2, NULL, '2025-03-20', NULL, NULL),
(13, 5, NULL, NULL, 1, NULL),
(14, 6, NULL, NULL, 1, NULL),
(15, 5, NULL, NULL, 1, NULL),
(16, 8, NULL, NULL, NULL, 1000.05),
(17, 9, NULL, '2025-03-31', NULL, NULL),
(18, 9, NULL, '2025-03-30', NULL, NULL),
(19, 9, NULL, '2025-03-28', NULL, NULL),
(20, 2, NULL, '2025-03-06', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `halls`
--

CREATE TABLE `halls` (
  `id` int NOT NULL,
  `name` text,
  `seating` smallint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Список залов';

--
-- Дамп данных таблицы `halls`
--

INSERT INTO `halls` (`id`, `name`, `seating`) VALUES
(1, 'Зал 1', 50),
(2, 'Зал 2', 60),
(3, 'Зал 3', 100);

-- --------------------------------------------------------

--
-- Структура таблицы `halls_seats`
--

CREATE TABLE `halls_seats` (
  `id` int NOT NULL,
  `hall_id` int DEFAULT NULL,
  `seat_id` int DEFAULT NULL,
  `row_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Схема мест в залах';

--
-- Дамп данных таблицы `halls_seats`
--

INSERT INTO `halls_seats` (`id`, `hall_id`, `seat_id`, `row_id`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 1, 5, 1),
(6, 2, 1, 1),
(7, 2, 2, 1),
(9, 2, 3, 1),
(10, 2, 4, 1),
(11, 2, 5, 1),
(12, 3, 1, 1),
(13, 3, 2, 1),
(14, 3, 3, 1),
(15, 3, 4, 1),
(16, 3, 5, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `tikets`
--

CREATE TABLE `tikets` (
  `id` int NOT NULL,
  `timetable_seatings_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `price` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Купленные билеты';

--
-- Дамп данных таблицы `tikets`
--

INSERT INTO `tikets` (`id`, `timetable_seatings_id`, `user_id`, `price`) VALUES
(1, 1, 1, 450),
(3, 3, 5, 280),
(4, 5, 3, 400),
(5, 6, 2, 300),
(6, 7, 1, 700),
(7, 8, 4, 650);

-- --------------------------------------------------------

--
-- Структура таблицы `timetable`
--

CREATE TABLE `timetable` (
  `id` int NOT NULL,
  `time` int DEFAULT NULL,
  `hall_id` int DEFAULT NULL,
  `film_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Расписание сеансов';

--
-- Дамп данных таблицы `timetable`
--

INSERT INTO `timetable` (`id`, `time`, `hall_id`, `film_id`) VALUES
(1, 1740733200, 1, 1),
(2, 1740740400, 2, 2),
(3, 1740747600, 3, 3),
(4, 1740834000, 1, 1),
(6, 1740834000, 2, 2),
(5, 1740834000, 2, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `timetable_seatings`
--

CREATE TABLE `timetable_seatings` (
  `id` int NOT NULL,
  `timetable_id` int DEFAULT NULL,
  `hall_seat_id` int DEFAULT NULL,
  `price` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Стоимость места в привязке к расписанию';

--
-- Дамп данных таблицы `timetable_seatings`
--

INSERT INTO `timetable_seatings` (`id`, `timetable_id`, `hall_seat_id`, `price`) VALUES
(1, 1, 1, 500),
(3, 2, 1, 300),
(5, 1, 2, 500),
(6, 1, 3, 300),
(7, 2, 6, 700),
(8, 3, 12, 777);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` tinytext,
  `email` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `phone` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Зарегистрированные посетители кинотеатра';

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`) VALUES
(1, 'Иван', 'ivan@site.ru', '9111111111'),
(2, 'Дмитрий', 'dmitry@site.ru', '9222222222'),
(3, 'Алексей', 'alex@site.ru', '9333333333'),
(4, 'Юрий', 'uriy@site.ru', '9444444444'),
(5, 'Александр', 'alexandr@site.ru', '9555555555');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `films_attrs`
--
ALTER TABLE `films_attrs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `films_ibfk_1` (`film_id`),
  ADD KEY `films_ibfk_2` (`attr_id`);

--
-- Индексы таблицы `films_attrs_types`
--
ALTER TABLE `films_attrs_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `films_attrs_values`
--
ALTER TABLE `films_attrs_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `films_attrs_values_ibfk_1` (`attr_type_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `films`
--
ALTER TABLE `films`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `films_attrs`
--
ALTER TABLE `films_attrs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `films_attrs_types`
--
ALTER TABLE `films_attrs_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `films_attrs_values`
--
ALTER TABLE `films_attrs_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `films_attrs`
--
ALTER TABLE `films_attrs`
  ADD CONSTRAINT `films_ibfk_1` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `films_ibfk_2` FOREIGN KEY (`attr_id`) REFERENCES `films_attrs_values` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `films_attrs_values`
--
ALTER TABLE `films_attrs_values`
  ADD CONSTRAINT `films_attrs_values_ibfk_1` FOREIGN KEY (`attr_type_id`) REFERENCES `films_attrs_types` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
