<?php

require_once __DIR__.'/vendor/autoload.php';

use Faker\Factory as Faker;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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

// Конфигурация
$numTitles = 100;
$numCinemas = 5;
$numHallsPerCinema = 3;
$hallSizeY = 5;
$hallSizeX = 5;

$minTitleDuration = 60;
$maxTitleDuration = 360;

$sessionsFrom = date('Y-m-d');
$countDays = 10;

$requiredTicketsCount = 10000;

// Особенности залов
$hallFeatures = [
    ['name' => '3D', 'additional_price' => 3.5, 'description' => '3D-оборудование'],
    ['name' => 'VIP кресла', 'additional_price' => 5.0, 'description' => 'Повышенный комфорт'],
    ['name' => 'Dolby Atmos', 'additional_price' => 2.5, 'description' => 'Пространственный звук'],
    ['name' => 'IMAX', 'additional_price' => 4.0, 'description' => 'IMAX экран'],
];

$pdo->beginTransaction();
$pdo->exec(
    "
    TRUNCATE tickets, sessions, hall_seats, hall_feature_hall, hall_features, halls, titles, cinemas RESTART IDENTITY CASCADE
"
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
for ($i = 0; $i < $numTitles; $i++) {
    $name = $faker->realText(32);
    $duration = mt_rand($minTitleDuration, $maxTitleDuration);
    $insertTitle->execute([$name, sprintf('%02d:%02d:00', intdiv($duration, 60), $duration % 60)]);
    $titles[] = ['id' => $pdo->lastInsertId(), 'duration' => $duration];
    $cinemaProgress->advance();
}
$cinemaProgress->finish();
$output->writeln('');

// cinemas, halls, seats, sessions, tickets
$insertCinema = $pdo->prepare("INSERT INTO cinemas (name, address) VALUES (?, ?) RETURNING id");
$insertHall = $pdo->prepare("INSERT INTO halls (cinema_id, base_price, name) VALUES (?, ?, ?) RETURNING id");
$insertHallSeat = $pdo->prepare("INSERT INTO hall_seats (hall_id, row_number, seat_number) VALUES (?, ?, ?)");
$insertSession = $pdo->prepare(
    "INSERT INTO sessions (hall_id, title_id, additional_price, start_at, end_at) VALUES (?, ?, ?, ?, ?) RETURNING id"
);
$insertTicket = $pdo->prepare(
    "INSERT INTO tickets (session_id, hall_seat_id, price, created_at) VALUES (?, ?, ?, now())"
);
$insertHallFeature = $pdo->prepare("INSERT INTO hall_feature_hall (hall_id, feature_id) VALUES (?, ?)");

$sectionCinemas = $output->section();
$hallSection = $output->section();
$seatsSection = $output->section();
$daysSection = $output->section();
$ticketsSection = $output->section();

$cinemaProgress = new ProgressBar($sectionCinemas, $numCinemas);
$cinemaProgress->setFormat('Cinemas: %current%/%max% [%bar%] %percent:3s%%');
$cinemaProgress->start();

$featureIds = $pdo->query("SELECT id FROM hall_features")->fetchAll(PDO::FETCH_COLUMN);
$ticketsCount = 0;

for ($c = 0; $c < $numCinemas; $c++) {
    $name = $faker->colorName().' '.$faker->city();
    $address = $faker->address();
    $insertCinema->execute([$name, $address]);
    $cinemaId = $pdo->lastInsertId();


    $hallSection->clear();
    $hallProgress = new ProgressBar($hallSection, $numHallsPerCinema);
    $hallProgress->setFormat('Halls: %current%/%max% [%bar%] %percent:3s%%');
    $hallProgress->start();

    for ($h = 0; $h < $numHallsPerCinema; $h++) {
        $basePrice = mt_rand(7, 15);
        $hallName = $faker->monthName().' '.strtoupper($faker->lexify('Hall ??'));
        $insertHall->execute([$cinemaId, $basePrice, $hallName]);
        $hallId = $pdo->lastInsertId();


        // особенности
        $selectedFeatures = array_rand($featureIds, rand(1, count($featureIds)));
        if (! is_array($selectedFeatures)) {
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
                $insertHallSeat->execute([$hallId, $y, $x]);
                $seatIds[] = $pdo->lastInsertId();
                $seatsProgress->advance();
            }
        }
        $seatsProgress->finish();

        // сеансы и билеты
        $daysSection->clear();
        $daysProgress = new ProgressBar($daysSection, $countDays);
        $daysProgress->setFormat('Days: %current%/%max% [%bar%] %percent:3s%%');
        $daysProgress->start();

        $currentDay = new DateTimeImmutable($sessionsFrom);
        $endDay = $currentDay->add(new DateInterval("P{$countDays}D"));
        while ($currentDay < $endDay) {
            $title = $titles[array_rand($titles)];
            $start = $currentDay;
            $end = $start->add(new DateInterval("PT{$title['duration']}M"));
            $additionalPrice = mt_rand(0, 5);
            $insertSession->execute(
                [$hallId, $title['id'], $additionalPrice, $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')]
            );
            $sessionId = $pdo->lastInsertId();

            $numTickets = mt_rand(20, count($seatIds));
            $usedSeats = array_rand($seatIds, $numTickets);
            if (! is_array($usedSeats)) {
                $usedSeats = [$usedSeats];
            }

            $ticketsSection->clear();
            $ticketsProgress = new ProgressBar($ticketsSection, $numTickets);
            $ticketsProgress->setFormat('Tickets: %current%/%max% [%bar%] %percent:3s%%');
            $ticketsProgress->start();
            foreach ($usedSeats as $sIndex) {
                $price = $basePrice + $additionalPrice + mt_rand(0, 3);
                $insertTicket->execute([$sessionId, $seatIds[$sIndex], $price]);
                $ticketsProgress->advance();
                $ticketsCount += 1;
                if ($ticketsCount >= $requiredTicketsCount) {
                    $ticketsProgress->finish();
                    break 4;
                }
            }
            $ticketsProgress->finish();

            $currentDay = $end->add(new DateInterval('PT5M'));
            $daysProgress->setProgress(($countDays - $currentDay->diff($endDay)->days));
        }
        $daysProgress->finish();
        $hallProgress->advance();
    }
    $hallProgress->finish();
    $cinemaProgress->advance();
}

$cinemaProgress->finish();
$output->writeln('');
$pdo->commit();

$output->writeln("Data generation completed successfully!");
$output->writeln("$ticketsCount tickets generated!");