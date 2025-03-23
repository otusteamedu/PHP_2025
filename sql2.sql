/*  Подсчёт проданных билетов за неделю */

SELECT count(id) 'Всего продано билетов' FROM `tikets` WHERE `buydate` >= NOW() - INTERVAL 7 DAY;