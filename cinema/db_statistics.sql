-- Oтсортированный список самых больших по размеру объектов БД (таблицы, включая индексы, сами индексы)
SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'cinema' ORDER BY DATA_LENGTH DESC;