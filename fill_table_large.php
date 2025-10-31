<?php

ini_set('memory_limit', '-1');
set_time_limit(0);

require_once 'vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

$host = 'localhost';
$dbname = 'cinema';
$user = 'app';
$password = 'app';
$port = '5432';

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
    echo "Успешное подключение к базе данных!\n";
} catch (\PDOException $e) {
    exit('Ошибка подключения к БД: '.$e->getMessage());
}

$totalTicketsToGenerate = 10000000;
$newShowtimesToGenerate = 50000;
$csvFilePath = __DIR__.'/tickets_large.csv';
$tempTableName = 'ticket_temp';

try {
    echo "Шаг 1/7: Добавляем {$newShowtimesToGenerate} новых сеансов...\n";
    $hallIds = $pdo->query('SELECT id FROM hall')->fetchAll(PDO::FETCH_COLUMN);
    $movies = $pdo->query('SELECT id, duration_minutes FROM movie')->fetchAll(PDO::FETCH_KEY_PAIR);

    if (empty($hallIds) || empty($movies)) {
        exit("Ошибка: Таблицы hall или movie пусты. Сначала запустите fill_table.php\n");
    }

    $stmt = $pdo->prepare(
        'INSERT INTO showtime (hall_id, movie_id, starts_at, ends_at, base_price) VALUES (?, ?, ?, ?, ?) ON CONFLICT (hall_id, starts_at) DO NOTHING'
    );

    $pdo->beginTransaction();
    for ($i = 0; $i < $newShowtimesToGenerate; $i++) {
        $movieId = array_rand($movies);
        $duration = $movies[$movieId];
        $startsAt = $faker->dateTimeBetween('+1 day', '+3 months');
        $endsAt = (clone $startsAt)->add(new DateInterval('PT'.$duration.'M'));

        $stmt->execute([
            $hallIds[array_rand($hallIds)],
            $movieId,
            $startsAt->format('Y-m-d H:i:s'),
            $endsAt->format('Y-m-d H:i:s'),
            $faker->randomFloat(2, 250, 800),
        ]);
    }
    $pdo->commit();
    echo "Новые сеансы успешно добавлены.\n";

    echo "Шаг 2/7: Получаем все ID из связанных таблиц (включая новые)...\n";
    $showtimeIds = $pdo->query('SELECT id FROM showtime')->fetchAll(PDO::FETCH_COLUMN);
    $seatIds = $pdo->query('SELECT id FROM seat')->fetchAll(PDO::FETCH_COLUMN);
    $customerIds = $pdo->query('SELECT id FROM customer')->fetchAll(PDO::FETCH_COLUMN);

    if (empty($showtimeIds) || empty($seatIds) || empty($customerIds)) {
        exit("Ошибка: Связанные таблицы (showtime, seat, customer) пусты. Сначала запустите populate.php\n");
    }

    echo "Шаг 3/7: Генерируем {$totalTicketsToGenerate} записей в CSV файл (это займет много времени)...\n";
    $fp = fopen($csvFilePath, 'w');
    if (! $fp) {
        exit("Не удалось открыть файл для записи: {$csvFilePath}\n");
    }

    for ($i = 0; $i < $totalTicketsToGenerate; $i++) {
        $showtimeId = $showtimeIds[array_rand($showtimeIds)];
        $seatId = $seatIds[array_rand($seatIds)];
        $customerId = $customerIds[array_rand($customerIds)];
        $status = 'sold';
        $price = $faker->randomFloat(2, 300, 1200);
        $soldAt = $faker->dateTimeThisYear()->format('Y-m-d H:i:s');

        fputcsv($fp, [$showtimeId, $seatId, $status, $price, $soldAt, $customerId], "\t");

        if (($i + 1) % 500000 == 0) {
            echo '  ...сгенерировано '.($i + 1)." записей\n";
        }
    }
    fclose($fp);
    echo "CSV файл успешно создан: {$csvFilePath}\n";

    echo "Шаг 4/7: Создаем временную таблицу '{$tempTableName}'...\n";
    $pdo->beginTransaction();
    // Создаем временную таблицу, которая будет удалена в конце сессии
    $pdo->exec("CREATE TEMP TABLE {$tempTableName} (LIKE ticket INCLUDING DEFAULTS)");

    echo "Шаг 5/7: Загружаем данные из CSV во временную таблицу с помощью COPY...\n";
    $pdo->pgsqlCopyFromFile($tempTableName, $csvFilePath, "\t", 'NULL', 'showtime_id, seat_id, status, price_paid, sold_at, customer_id');
    echo "Данные успешно загружены во временную таблицу.\n";

    echo "Шаг 6/7: Переносим данные в основную таблицу 'ticket', игнорируя дубликаты...\n";
    $insertQuery = "
        INSERT INTO ticket (showtime_id, seat_id, status, price_paid, sold_at, customer_id)
        SELECT showtime_id, seat_id, status, price_paid, sold_at, customer_id
        FROM {$tempTableName}
        ON CONFLICT (showtime_id, seat_id) DO NOTHING
    ";
    $insertedRows = $pdo->exec($insertQuery);
    echo "Успешно перенесено {$insertedRows} уникальных записей.\n";

    $pdo->commit();
    echo "Шаг 7/7: Транзакция успешно завершена.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "\nПроизошла ошибка: ".$e->getMessage()."\n";
    echo "Транзакция отменена, изменения не сохранены.\n";
} finally {
    if (file_exists($csvFilePath)) {
        unlink($csvFilePath);
        echo "Временный CSV файл удален.\n";
    }
}

echo "\nСкрипт завершил работу.\n";
