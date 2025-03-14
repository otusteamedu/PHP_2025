-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: mysql
-- Время создания: Мар 14 2025 г., 10:42
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
-- База данных: `test2`
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
(12, 3, 15);

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
(7, 'Золотой граммофон', 'boolean');

-- --------------------------------------------------------

--
-- Структура таблицы `films_attrs_values`
--

CREATE TABLE `films_attrs_values` (
  `id` int NOT NULL,
  `attr_type_id` int DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `date` date DEFAULT NULL,
  `boolean` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `films_attrs_values`
--

INSERT INTO `films_attrs_values` (`id`, `attr_type_id`, `text`, `date`, `boolean`) VALUES
(1, 3, NULL, '2025-03-28', NULL),
(2, 1, 'Рецензия на фильм 1', NULL, NULL),
(3, 1, 'Рецензия на фильм 2', NULL, NULL),
(4, 1, 'Рецензия на фильм 3', NULL, NULL),
(5, 2, NULL, '2025-03-14', NULL),
(6, 2, NULL, '2025-03-15', NULL),
(7, 2, NULL, '2025-03-16', NULL),
(8, 4, NULL, '2025-03-27', NULL),
(9, 4, NULL, '2025-03-30', NULL),
(10, 4, NULL, '2025-03-25', NULL),
(11, 2, NULL, '2025-03-19', NULL),
(12, 2, NULL, '2025-03-20', NULL),
(13, 5, NULL, NULL, 1),
(14, 6, NULL, NULL, 1),
(15, 5, NULL, NULL, 1);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `films_attrs_types`
--
ALTER TABLE `films_attrs_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `films_attrs_values`
--
ALTER TABLE `films_attrs_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
