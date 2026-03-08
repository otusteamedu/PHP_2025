<?php

/**
 * Конфигурационный файл для подключения к базе данных
 * 
 * ВНИМАНИЕ: Этот файл содержит чувствительные данные!
 * Не коммитьте его в систему контроля версий.
 * Используйте config.example.php как шаблон.
 */

return [
    'host' => 'localhost',
    'dbname' => 'test_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
