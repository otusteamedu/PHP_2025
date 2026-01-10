<?php

require_once __DIR__ . '/vendor/autoload.php';

use BookstoreApp\Infrastructure\Database\Connection;
use BookstoreApp\Infrastructure\Database\IdentityMap;
use BookstoreApp\Application\Command\TableGatewayCommand;
use BookstoreApp\Application\Command\RawGatewayCommand;
use BookstoreApp\Application\Command\ActiveRecordCommand;
use BookstoreApp\Application\Command\DataMapperCommand;

$config = require __DIR__ . '/config/database.php';

try {
    $connection = new Connection($config);
    $testConnection = $connection->getConnection();
    $testConnection->query('SELECT 1');
    echo "Подключение к базе данных успешно".PHP_EOL;
} catch (Exception $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage() . PHP_EOL;;
    echo "Проверьте настройки базы данных в файле config/database.php" . PHP_EOL;;
    exit(1);
}

$identityMap = new IdentityMap();

$commands = [
    'table-gateway' => new TableGatewayCommand($connection),
    'raw-gateway' => new RawGatewayCommand($connection),
    'active-record' => new ActiveRecordCommand($connection),
    'data-mapper' => new DataMapperCommand($connection, $identityMap),
];

if ($argc < 2) {
    echo "Использование: php console.php <команда> [опции]". PHP_EOL;
    echo "Доступные команды:". PHP_EOL;;
    foreach ($commands as $name => $command) {
        echo "  $name - " . $command->getDescription() . "". PHP_EOL;;
    }
    echo "Примеры:". PHP_EOL;
    echo "  php console.php table-gateway list". PHP_EOL;;
    echo "  php console.php table-gateway city Москва". PHP_EOL;;
    echo "  php console.php raw-gateway cafe". PHP_EOL;;
    echo "  php console.php raw-gateway rating 4.5". PHP_EOL;
    echo "  php console.php active-record list". PHP_EOL;
    echo "  php console.php active-record demo". PHP_EOL;
    echo "  php console.php data-mapper collection". PHP_EOL;
    echo "  php console.php data-mapper identity". PHP_EOL;
    exit(1);
}

$commandName = $argv[1];
$args = array_slice($argv, 2);

if (!isset($commands[$commandName])) {
    echo "Неизвестная команда: $commandName". PHP_EOL;
    echo "Доступные команды: " . implode(', ', array_keys($commands)) . PHP_EOL;
    exit(1);
}

try {
    $commands[$commandName]->execute($args);
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . PHP_EOL;
    exit(1);
}