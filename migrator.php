<?php

use Database\DatabaseMigrator;

require __DIR__ . '/vendor/autoload.php';

try {
    $migrator = new DatabaseMigrator();
    $migrator->runMigrations();
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}