<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory as Faker;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

include 'ChunkInsert.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$loaded = $dotenv->safeLoad();

$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPassword = $_ENV['DB_PASSWORD'];
$dbHost = $_ENV['DB_HOST'];
$dbPort = $_ENV['DB_PORT'];

// Подключение к БД
$pdo = new PDO("pgsql:host=$dbHost;port=$dbPort;dbname=$dbName;", $dbUser, $dbPassword, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$faker = Faker::create('ru_RU');
$output = new ConsoleOutput();
$sessionsFrom = date('Y-m-d 00:00:00');

// Конфигурация
$neededRecordsCount = 10_000_000;

// Базовые настройки
$numTitles = 19;
$numCinemas = 1;
$numHallsPerCinema = 25;
$hallSizeY = 14;
$hallSizeX = 14;
$numTickets = 19;
$duration = 144;
$featuresPerHall = 2;

// ---- Статика ----
$totalHallsCount = $numCinemas * $numHallsPerCinema;
$totalSeatsCount = $totalHallsCount * ($hallSizeX * $hallSizeY);

$hallFeatures = [
    ['name' => '3D', 'additional_price' => 3.5, 'description' => '3D-оборудование'],
    ['name' => 'VIP кресла', 'additional_price' => 5.0, 'description' => 'Повышенный комфорт'],
    ['name' => 'Dolby Atmos', 'additional_price' => 2.5, 'description' => 'Пространственный звук'],
    ['name' => 'IMAX', 'additional_price' => 4.0, 'description' => 'IMAX экран'],
    ['name' => 'IMAX', 'additional_price' => 4.0, 'description' => 'IMAX экран'],
];
$totalHallFeaturesCount = count($hallFeatures);

// pivot hall <-> feature
$totalHallFeaturesPivotCount = $totalHallsCount * $featuresPerHall;

// ---- Динамика ----

// Сеансов на 1 день
$totalSessionsPerDay = (1440 / $duration) * ($numCinemas * $numHallsPerCinema);

// Билетов на 1 день
$totalTicketsPerDay = $totalSessionsPerDay * $numTickets;

// ---- Итог ----
$staticRecordsCount = $numCinemas + $totalHallsCount + $totalSeatsCount + $numTitles + $totalHallFeaturesCount + $totalHallFeaturesPivotCount;

$recordsPerDay = $totalSessionsPerDay + $totalTicketsPerDay;


$countDays = (int)(($neededRecordsCount - 250) / $recordsPerDay);

$totalRecordsCount = $staticRecordsCount + $recordsPerDay * $countDays;

echo 'Static records count: ' . number_format($staticRecordsCount) . PHP_EOL;
echo 'Records per day: ' . number_format($recordsPerDay) . PHP_EOL;
echo 'Total records count: ' . number_format($totalRecordsCount) . PHP_EOL;

$pdo->exec(
    "
    TRUNCATE tickets, sessions, hall_seats, hall_feature_hall, hall_features, halls, titles, cinemas RESTART IDENTITY CASCADE
",
);

// hall_features
$insertFeature = $pdo->prepare("INSERT INTO hall_features (name, additional_price, description) VALUES (?, ?, ?)");
$cinemaProgress = new ProgressBar($output, count($hallFeatures));
$cinemaProgress->setFormat('Features: %current%/%max% [%bar%] %percent:3s%%');
$cinemaProgress->start();
foreach ($hallFeatures as $f) {
    $insertFeature->execute([$f['name'], $f['additional_price'], $f['description']]);
    $cinemaProgress->advance();
}
$cinemaProgress->finish();
$output->writeln('');

// titles
$titles = [];
$insertTitle = $pdo->prepare("INSERT INTO titles (title, duration) VALUES (?, ?) RETURNING id");
$cinemaProgress = new ProgressBar($output, $numTitles);
$cinemaProgress->setFormat('Titles: %current%/%max% [%bar%] %percent:3s%%');
$cinemaProgress->start();

$titleDuration = new DateTime()->setTime(0, 0);
$titleDuration->modify("+{$duration} minutes");
for ($i = 0; $i < $numTitles; $i++) {
    $name = $faker->realText(32);

    $insertTitle->execute([$name, $titleDuration->format("H:i:s")]);
    $titles[] = ['id' => $pdo->lastInsertId(), 'duration' => $duration];
    $cinemaProgress->advance();
}
$cinemaProgress->finish();
$output->writeln('');

// cinemas, halls, seats, sessions, tickets
$insertCinema = $pdo->prepare("INSERT INTO cinemas (name, address) VALUES (?, ?) RETURNING id");
$insertHall = $pdo->prepare("INSERT INTO halls (cinema_id, base_price, name) VALUES (?, ?, ?) RETURNING id");
$insertHallFeature = $pdo->prepare("INSERT INTO hall_feature_hall (hall_id, feature_id) VALUES (?, ?)");

$hallSeatsChunkedInsert = new ChunkInsert($pdo, 'INSERT INTO hall_seats (hall_id, row_number, seat_number) VALUES', 3);
$hallSeatsSelectQuery = $pdo->prepare('SELECT id FROM hall_seats WHERE hall_id = ?');

$ticketsInsert = new ChunkInsert($pdo, 'INSERT INTO tickets (session_id, hall_seat_id, price) VALUES', 3);

$sessionsInsert = new ChunkInsert(
    $pdo, 'INSERT INTO sessions (hall_id, title_id, additional_price, start_at, end_at) VALUES', 3,
);
$sessionsSelectQuery = $pdo->prepare("SELECT id FROM sessions WHERE hall_id = ?");


$sectionCinemas = $output->section();
$hallSection = $output->section();
$seatsSection = $output->section();
$daysSection = $output->section();
$ticketsSection = $output->section();

$cinemaProgress = new ProgressBar($sectionCinemas, $numCinemas);
$cinemaProgress->setFormat('Cinemas: %current%/%max% [%bar%] %percent:3s%%');
$cinemaProgress->start();

$featureIds = $pdo->query("SELECT id FROM hall_features")->fetchAll(PDO::FETCH_COLUMN);

for ($c = 0; $c < $numCinemas; $c++) {
    $name = $faker->colorName() . ' ' . $faker->city();
    $address = $faker->address();
    $insertCinema->execute([$name, $address]);
    $cinemaId = $pdo->lastInsertId();

    $hallSection->clear();
    $hallProgress = new ProgressBar($hallSection, $numHallsPerCinema);
    $hallProgress->setFormat('Halls: %current%/%max% [%bar%] %percent:3s%%');
    $hallProgress->start();

    for ($h = 0; $h < $numHallsPerCinema; $h++) {
        $basePrice = mt_rand(7, 15);
        $hallName = $faker->monthName() . ' ' . strtoupper($faker->lexify('Hall ??'));
        $insertHall->execute([$cinemaId, $basePrice, $hallName]);
        $hallId = $pdo->lastInsertId();

        // особенности
        $selectedFeatures = array_rand($featureIds, $featuresPerHall);
        if (!is_array($selectedFeatures)) {
            $selectedFeatures = [$selectedFeatures];
        }
        foreach ($selectedFeatures as $fIndex) {
            $insertHallFeature->execute([$hallId, $featureIds[$fIndex]]);
        }

        // места
        $seatsSection->clear();
        $seatsProgress = new ProgressBar($seatsSection, $hallSizeY * $hallSizeX);
        $seatsProgress->setFormat('Seats: %current%/%max% [%bar%] %percent:3s%%');
        $seatsProgress->start();
        $seatIds = [];
        for ($y = 1; $y <= $hallSizeY; $y++) {
            for ($x = 1; $x <= $hallSizeX; $x++) {
                $hallSeatsChunkedInsert->addRow([$hallId, $y, $x]);
                $seatsProgress->advance();
            }
        }
        $seatsSection->clear();
        $hallSeatsChunkedInsert->insert();

        $hallSeatsSelectQuery->execute([$hallId]);
        while (($hallSeatId = $hallSeatsSelectQuery->fetch(PDO::FETCH_COLUMN))) {
            $seatIds[] = $hallSeatId;
        }

        // сеансы и билеты
        $daysSection->clear();
        $daysProgress = new ProgressBar($daysSection, $countDays);
        $daysProgress->setFormat('Days: %current%/%max% [%bar%] %percent:3s%%');
        $daysProgress->start();

        $currentDay = new DateTimeImmutable($sessionsFrom);
        $endDay = $currentDay->add(new DateInterval("P{$countDays}D"));
        $additionalPrice = mt_rand(0, 5);
        while ($currentDay < $endDay) {
            $title = $titles[array_rand($titles)];
            $start = $currentDay;
            $end = $start->add(new DateInterval("PT{$title['duration']}M"));

            $sessionsInsert->addRow(
                [
                    $hallId,
                    $title['id'],
                    $additionalPrice,
                    "'{$start->format('Y-m-d H:i:s')}'",
                    "'{$end->format('Y-m-d H:i:s')}'",
                ],
            );
            $currentDay = $end;
            $daysProgress->setProgress(($countDays - $currentDay->diff($endDay)->days));
        }
        $sessionsInsert->insert();

        $ticketsSection->clear();
        $ticketsProgress = new ProgressBar($ticketsSection, $numTickets * (1440 / $duration));
        $ticketsProgress->setFormat('Tickets: %current%/%max% [%bar%] %percent:3s%%');
        $ticketsProgress->start();
        $sessionsSelectQuery->execute([$hallId]);
        while (($sessionId = $sessionsSelectQuery->fetch(PDO::FETCH_COLUMN))) {
            $usedSeats = array_rand($seatIds, $numTickets);
            if (!is_array($usedSeats)) {
                $usedSeats = [$usedSeats];
            }
            foreach ($usedSeats as $sIndex) {
                $price = $basePrice + $additionalPrice + mt_rand(0, 3);
                $ticketsInsert->addRow([$sessionId, $seatIds[$sIndex], $price]);
            }
            $ticketsProgress->advance($numTickets);
        }

        $ticketsInsert->insert();
        $daysProgress->finish();
        $hallProgress->advance();
    }
    $hallProgress->finish();
    $cinemaProgress->advance();
}

$cinemaProgress->finish();
$output->writeln('');

$output->writeln("Data generation completed successfully!");