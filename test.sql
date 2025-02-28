-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: mysql
-- Время создания: Фев 28 2025 г., 13:27
-- Версия сервера: 9.1.0
-- Версия PHP: 8.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `films`
--

CREATE TABLE `films` (
  `id` int NOT NULL,
  `name` text
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
-- Структура таблицы `halls`
--

CREATE TABLE `halls` (
  `id` int NOT NULL,
  `name` text,
  `seating` smallint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `halls`
--

INSERT INTO `halls` (`id`, `name`, `seating`) VALUES
(1, 'Зал 1', 50),
(2, 'Зал 2', 60),
(3, 'Зал 3', 100);

-- --------------------------------------------------------

--
-- Структура таблицы `tikets`
--

CREATE TABLE `tikets` (
  `id` int NOT NULL,
  `timetable_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `tikets`
--

INSERT INTO `tikets` (`id`, `timetable_id`, `user_id`) VALUES
(4, 1, 1),
(3, 1, 2),
(1, 1, 3),
(12, 1, 4),
(13, 1, 5),
(7, 2, 1),
(6, 2, 2),
(5, 2, 3),
(14, 2, 5),
(10, 3, 1),
(9, 3, 2),
(8, 3, 3),
(15, 6, 2),
(16, 6, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `timetable`
--

CREATE TABLE `timetable` (
  `id` int NOT NULL,
  `time` int DEFAULT NULL,
  `hall_id` int DEFAULT NULL,
  `film_id` int DEFAULT NULL,
  `price` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `timetable`
--

INSERT INTO `timetable` (`id`, `time`, `hall_id`, `film_id`, `price`) VALUES
(1, 1740733200, 1, 1, 500),
(2, 1740740400, 2, 2, 600),
(3, 1740747600, 3, 3, 300),
(4, 1740834000, 1, 1, 500),
(5, 1740834000, 2, 3, 400),
(6, 1740834000, 2, 2, 550);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` tinytext,
  `email` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `phone` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `halls`
--
ALTER TABLE `halls`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tikets`
--
ALTER TABLE `tikets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `timetable_id` (`timetable_id`,`user_id`),
  ADD KEY `user_id_fk` (`user_id`);

--
-- Индексы таблицы `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `time` (`time`,`hall_id`,`film_id`),
  ADD KEY `film_id_fk` (`film_id`),
  ADD KEY `hall_id_fk` (`hall_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `films`
--
ALTER TABLE `films`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `halls`
--
ALTER TABLE `halls`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `tikets`
--
ALTER TABLE `tikets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `tikets`
--
ALTER TABLE `tikets`
  ADD CONSTRAINT `tikets_ibfk_1` FOREIGN KEY (`timetable_id`) REFERENCES `timetable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tikets_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `timetable_ibfk_2` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
