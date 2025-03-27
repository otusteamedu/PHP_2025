<?php
$envFile = dirname(__DIR__) . '/.env';
if (!file_exists($envFile)) {
  die("Файл .env не найден!");
}

$envVariables = parse_ini_file($envFile);

$requiredVariables = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'GENERATE_ROWS'];
foreach ($requiredVariables as $variable) {
  if (!isset($envVariables[$variable])) {
    die("Переменная $variable не найдена в .env!");
  }
}

try {
  $dsn = "pgsql:host={$envVariables['DB_HOST']};port={$envVariables['DB_PORT']};dbname={$envVariables['DB_NAME']}";
  $pdo = new PDO($dsn, $envVariables['DB_USER'], $envVariables['DB_PASSWORD']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Ошибка подключения к базе данных: " . $e->getMessage());
}

function generateRandomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $randomString;
}

$totalRows = (int)$envVariables['GENERATE_ROWS'];
$batchSize = 10000;  // Размер пакета для вставки

// Распределение строк по таблицам
$cinemaCount = max(1, round($totalRows * 0.0001)); // 0.01%
$hallCount = max(1, round($totalRows * 0.0005)); // 0.05%
$seatCount = max(1, round($totalRows * 0.005)); // 0.5%
$movieCount = max(1, round($totalRows * 0.001)); // 0.1%
$sessionCount = max(1, round($totalRows * 0.01)); // 1%
$clientCount = max(1, round($totalRows * 0.5)); // 50%
$ticketCount = $totalRows - ($cinemaCount + $hallCount + $seatCount + $movieCount + $sessionCount + $clientCount); // Остальное

$startTime = microtime(true);

$pdo->beginTransaction(); // Начало транзакции

// Кинотеатры
$stmtCinema = $pdo->prepare("INSERT INTO Cinema (name, address) VALUES (?, ?)");
for ($i = 1; $i <= $cinemaCount; $i++) {
  $stmtCinema->execute([
      "Кинотеатр " . generateRandomString(5),
      "ул. " . generateRandomString(10) . ", " . rand(1, 100)
  ]);
}
echo "Добавлено $cinemaCount кинотеатров\n";

// Залы (получаем ID один раз)
$cinemaIds = $pdo->query("SELECT id FROM Cinema")->fetchAll(PDO::FETCH_COLUMN);
$stmtHall = $pdo->prepare("INSERT INTO Hall (cinema_id, name, capacity) VALUES (?, ?, ?)");
for ($i = 1; $i <= $hallCount; $i++) {
  $stmtHall->execute([
      $cinemaIds[array_rand($cinemaIds)],
      "Зал " . $i,
      rand(50, 200)
  ]);
}
echo "Добавлено $hallCount залов\n";

// Места
$hallIds = $pdo->query("SELECT id FROM Hall")->fetchAll(PDO::FETCH_COLUMN);
$stmtSeat = $pdo->prepare("INSERT INTO Seat (hall_id, row_number, seat_number, type) VALUES (?, ?, ?, ?)");
for ($i = 1; $i <= $seatCount; $i++) {
  $stmtSeat->execute([
      $hallIds[array_rand($hallIds)],
      rand(1, 10),
      rand(1, 20),
      rand(0, 1) ? 'standard' : 'vip'
  ]);
}
echo "Добавлено $seatCount мест\n";

// Фильмы
$stmtMovie = $pdo->prepare("INSERT INTO Movie (title, duration, rating) VALUES (?, ?, ?)");
for ($i = 1; $i <= $movieCount; $i++) {
  $stmtMovie->execute([
      "Фильм " . generateRandomString(10),
      rand(60, 180),
      ['PG-13', 'R', 'G'][rand(0, 2)]
  ]);
}
echo "Добавлено $movieCount фильмов\n";

// Сеансы
$movieIds = $pdo->query("SELECT id FROM Movie")->fetchAll(PDO::FETCH_COLUMN);
$stmtSession = $pdo->prepare("INSERT INTO Session (movie_id, hall_id, start_time, price, format) VALUES (?, ?, ?, ?, ?)");
for ($i = 1; $i <= $sessionCount; $i++) {
  $stmtSession->execute([
      $movieIds[array_rand($movieIds)],
      $hallIds[array_rand($hallIds)],
      date('Y-m-d H:i:s', strtotime("+" . rand(1, 30) . " days")),
      rand(300, 1000),
      ['2D', '3D', 'IMAX'][rand(0, 2)]
  ]);
}
echo "Добавлено $sessionCount сеансов\n";

// Клиенты
$stmtClient = $pdo->prepare("INSERT INTO Client (name, email, phone) VALUES (?, ?, ?)");
for ($i = 1; $i <= $clientCount; $i++) {
  $stmtClient->execute([
      "Клиент " . $i,
      generateRandomString(10) . '@example.com',
      '+7' . generateRandomString(10)
  ]);
}
echo "Добавлено $clientCount клиентов\n";

// Билеты
$sessionIds = $pdo->query("SELECT id FROM Session")->fetchAll(PDO::FETCH_COLUMN);
$seatIds = $pdo->query("SELECT id FROM Seat")->fetchAll(PDO::FETCH_COLUMN);
$clientIds = $pdo->query("SELECT id FROM Client")->fetchAll(PDO::FETCH_COLUMN);
$stmtTicket = $pdo->prepare("INSERT INTO Ticket (session_id, seat_id, client_id, price) VALUES (?, ?, ?, ?)");
for ($i = 1; $i <= $ticketCount; $i++) {
  $stmtTicket->execute([
      $sessionIds[array_rand($sessionIds)],
      $seatIds[array_rand($seatIds)],
      $clientIds[array_rand($clientIds)],
      rand(300, 1000)
  ]);
}
echo "Добавлено $ticketCount билетов\n";

$pdo->commit(); // Фиксация транзакции

$endTime = microtime(true);
$executionTime = $endTime - $startTime;

$totalInserted = $cinemaCount + $hallCount + $seatCount + $movieCount + $sessionCount + $clientCount + $ticketCount;
echo "Генерация данных завершена!\n";
echo "Всего добавлено записей: $totalInserted\n";
echo "Общее время выполнения: " . round($executionTime, 2) . " секунд\n";