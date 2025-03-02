<?php

session_start();

$count = $_SESSION['count'] ?? 1;

echo "Проверка сохранения сессии в Redis: страница перезагружена $count раз(а)";

$_SESSION['count'] = ++$count;

session_write_close();