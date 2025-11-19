<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Database\Database;

try {
    echo "🔧 Loading configuration...\n";
    Config::load();
    
    echo "🔌 Testing database connection...\n";
    $db = Database::getConnection();
    
    echo "✅ Database connection successful!\n";
    
    // Проверяем существование базы данных
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $dbName = $stmt->fetch()['db_name'];
    echo "📊 Current database: {$dbName}\n";
    
    // Показываем таблицы
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "📝 No tables found in database.\n";
    } else {
        echo "📋 Tables in database: " . implode(', ', $tables) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}