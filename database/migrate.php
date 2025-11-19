<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database\Database;

Config::load();

$migrations = [
    'create_tasks_table' => require __DIR__ . '/migrations/001_create_tasks_table.php',
    'create_user_states_table' => require __DIR__ . '/migrations/002_create_user_states_table.php',
    'create_reminder_fields' => require __DIR__ . '/migrations/003_add_reminder_fields_to_tasks.php',
];

$db = Database::getConnection();

foreach ($migrations as $name => $sql) {
    echo "Running migration: {$name}\n";
    $db->exec($sql);
    echo "Migration {$name} completed successfully\n";
}

echo "All migrations completed!\n";