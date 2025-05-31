<?php
// Получаем имя контейнера
$containerName = gethostname();

// Получаем текущую временную метку
$timestamp = date('Y-m-d H:i:s');

// Выводим информацию о контейнере и времени
echo "Запрос обработан контейнером app: $containerName<br>";
echo "Время обработки: $timestamp<br>";