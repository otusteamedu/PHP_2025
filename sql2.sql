/*  Подсчёт проданных билетов за неделю */

SELECT * FROM `tikets` WHERE `buydate` >= NOW() - INTERVAL 7 DAY;