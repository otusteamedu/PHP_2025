<?php

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
    throw new \PDOException($e->getMessage(), (int) $e->getCode());
}

$pdo->beginTransaction();
echo "Начинаем заполнение таблиц...\n";

try {
    echo "Заполняем залы...\n";
    $halls = [];
    $stmt = $pdo->prepare('INSERT INTO hall (name, seat_rows, seat_columns) VALUES (?, ?, ?) RETURNING id');
    for ($i = 0; $i < 10; $i++) {
        $stmt->execute([
            'Зал '.($i + 1),
            $faker->numberBetween(8, 15),
            $faker->numberBetween(10, 20),
        ]);
        $halls[] = $stmt->fetchColumn();
    }

    echo "Заполняем типы мест...\n";
    $seatTypes = [
        ['standard', 'Стандарт', 0],
        ['vip', 'VIP', 150.00],
        ['love-seat', 'Места для двоих', 100.00],
    ];
    $seatTypeIds = [];
    $stmt = $pdo->prepare('INSERT INTO seat_type (code, name, price_modifier) VALUES (?, ?, ?) RETURNING id');
    foreach ($seatTypes as $type) {
        $stmt->execute($type);
        $seatTypeIds[$type[0]] = $stmt->fetchColumn();
    }

    echo "Генерируем места в залах...\n";
    $seats = [];
    $hallDetails = $pdo->query('SELECT id, seat_rows, seat_columns FROM hall')->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare('INSERT INTO seat (hall_id, row_number, seat_number, seat_type_id) VALUES (?, ?, ?, ?)');
    foreach ($hallDetails as $hall) {
        for ($row = 1; $row <= $hall['seat_rows']; $row++) {
            for ($seatNum = 1; $seatNum <= $hall['seat_columns']; $seatNum++) {
                // Первые 2 ряда - VIP
                $typeId = ($row <= 2) ? $seatTypeIds['vip'] : $seatTypeIds['standard'];
                $stmt->execute([$hall['id'], $row, $seatNum, $typeId]);
            }
        }
    }
    $seatIds = $pdo->query('SELECT id FROM seat')->fetchAll(PDO::FETCH_COLUMN);

    echo "Заполняем фильмы...\n";
    $movies = [];
    $stmt = $pdo->prepare('INSERT INTO movie (title, duration_minutes, rating, release_date) VALUES (?, ?, ?, ?)');
    for ($i = 0; $i < 50; $i++) {
        $stmt->execute([
            $faker->realText(40),
            $faker->numberBetween(80, 180),
            $faker->randomElement(['G', 'PG', 'PG-13', 'R', 'NC-17']),
            $faker->dateTimeThisYear()->format('Y-m-d'),
        ]);
    }
    $movieIds = $pdo->query('SELECT id FROM movie')->fetchAll(PDO::FETCH_COLUMN);

    echo "Заполняем сеансы...\n";
    $showtimes = [];
    $stmt = $pdo->prepare('INSERT INTO showtime (hall_id, movie_id, starts_at, ends_at, base_price) VALUES (?, ?, ?, ?, ?)');
    for ($i = 0; $i < 200; $i++) {
        $movieId = $faker->randomElement($movieIds);
        $duration = $pdo->query("SELECT duration_minutes FROM movie WHERE id = $movieId")->fetchColumn();
        $startsAt = $faker->dateTimeBetween('now', '+1 month');
        $endsAt = (clone $startsAt)->add(new DateInterval('PT'.$duration.'M'));

        $stmt->execute([
            $faker->randomElement($halls),
            $movieId,
            $startsAt->format('Y-m-d H:i:s'),
            $endsAt->format('Y-m-d H:i:s'),
            $faker->randomFloat(2, 250, 800),
        ]);
    }
    $showtimeIds = $pdo->query('SELECT id FROM showtime')->fetchAll(PDO::FETCH_COLUMN);

    echo "Заполняем клиентов...\n";
    $stmt = $pdo->prepare('INSERT INTO customer (email, phone, full_name) VALUES (?, ?, ?)');
    for ($i = 0; $i < 1000; $i++) {
        $stmt->execute([
            $faker->unique()->email,
            $faker->phoneNumber,
            $faker->name,
        ]);
    }
    $customerIds = $pdo->query('SELECT id FROM customer')->fetchAll(PDO::FETCH_COLUMN);

    echo "Заполняем билеты (10000 штук)...\n";
    $stmt = $pdo->prepare(
        'INSERT INTO ticket (showtime_id, seat_id, status, price_paid, sold_at, customer_id) 
         VALUES (?, ?, ?, ?, ?, ?)
         ON CONFLICT (showtime_id, seat_id) DO NOTHING'
    );

    for ($i = 0; $i < 10000; $i++) {
        $showtimeId = $faker->randomElement($showtimeIds);
        $seatId = $faker->randomElement($seatIds);
        $customerId = $faker->randomElement($customerIds);
        $status = 'sold';
        $price = $faker->randomFloat(2, 300, 1200);
        $soldAt = $faker->dateTimeThisMonth();

        $stmt->execute([$showtimeId, $seatId, $status, $price, $soldAt->format('Y-m-d H:i:s'), $customerId]);
        if ($i % 1000 == 0) {
            echo "  ...добавлено $i билетов\n";
        }
    }

    $pdo->commit();
    echo "\nВсе таблицы успешно заполнены!\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "\nПроизошла ошибка: ".$e->getMessage()."\n";
    echo "Транзакция отменена, изменения не сохранены.\n";
}
