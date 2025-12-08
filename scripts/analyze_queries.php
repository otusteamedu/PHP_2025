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

$indexes = [
    "CREATE INDEX IF NOT EXISTS idx_session_start_time ON session (date(start_time))",
    "CREATE INDEX IF NOT EXISTS idx_session_time_range ON session (start_time)",
    "CREATE INDEX IF NOT EXISTS idx_ticket_session_id ON ticket (session_id)",
    "CREATE INDEX IF NOT EXISTS idx_session_movie_id ON session (movie_id)",
    "CREATE INDEX IF NOT EXISTS idx_ticket_session_price ON ticket (session_id, price)",
    "CREATE INDEX IF NOT EXISTS idx_seat_hall_id ON seat (hall_id)"
];

foreach ($indexes as $index) {
  try {
    $pdo->exec($index);
  } catch (PDOException $e) {
    echo "Ошибка при создании индекса: " . $e->getMessage() . "\n";
  }
}

// Увеличиваем work_mem для обработки сортировок в памяти
$pdo->exec("SET work_mem = '64MB'");

// Оригинальные запросы
$originalQueries = [
    "1. Выбор всех фильмов на сегодня" => "
        SELECT m.title
        FROM Movie m
        JOIN Session s ON m.id = s.movie_id
        WHERE DATE(s.start_time) = CURRENT_DATE;
    ",
    "2. Подсчёт проданных билетов за неделю" => "
        SELECT COUNT(*) AS tickets_sold
        FROM Ticket t
        JOIN Session s ON t.session_id = s.id
        WHERE s.start_time BETWEEN NOW() - INTERVAL '7 days' AND NOW();
    ",
    "3. Формирование афиши (фильмы, которые показывают сегодня)" => "
        SELECT DISTINCT m.title
        FROM Movie m
        JOIN Session s ON m.id = s.movie_id
        WHERE DATE(s.start_time) = CURRENT_DATE;
    ",
    "4. Поиск 3 самых прибыльных фильмов за неделю" => "
        SELECT m.title, SUM(t.price) AS total_revenue
        FROM Movie m
        JOIN Session s ON m.id = s.movie_id
        JOIN Ticket t ON s.id = t.session_id
        WHERE s.start_time BETWEEN NOW() - INTERVAL '7 days' AND NOW()
        GROUP BY m.title
        ORDER BY total_revenue DESC
        LIMIT 3;
    ",
    "5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс" => "
        SELECT 
            s.row_number, 
            s.seat_number,
            s.type,
            CASE 
                WHEN t.id IS NULL THEN 'Свободно' 
                ELSE 'Занято' 
            END AS status
        FROM Seat s
        LEFT JOIN Ticket t ON s.id = t.seat_id AND t.session_id = 1
        WHERE s.hall_id = (SELECT hall_id FROM Session WHERE id = 1)
        ORDER BY s.row_number, s.seat_number;
    ",
    "6. Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс" => "
        SELECT 
            MIN(price) AS min_price, 
            MAX(price) AS max_price
        FROM Ticket
        WHERE session_id = 1;
    "
];

// Оптимизированные запросы
$optimizedQueries = [
    "1. Выбор всех фильмов на сегодня (оптимизированный)" => "
        SELECT m.title
        FROM Movie m
        JOIN Session s ON m.id = s.movie_id
        WHERE s.start_time >= CURRENT_DATE 
          AND s.start_time < CURRENT_DATE + INTERVAL '1 day';
    ",
    "2. Подсчёт проданных билетов за неделю (оптимизированный)" => "
        SELECT COUNT(*) AS tickets_sold
        FROM Ticket t
        WHERE EXISTS (
            SELECT 1 FROM Session s 
            WHERE s.id = t.session_id 
              AND s.start_time BETWEEN NOW() - INTERVAL '7 days' AND NOW()
        );
    ",
    "3. Формирование афиши (оптимизированный)" => "
        SELECT m.title
        FROM Movie m
        WHERE EXISTS (
            SELECT 1 FROM Session s 
            WHERE s.movie_id = m.id 
              AND s.start_time >= CURRENT_DATE 
              AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
        );
    ",
    "4. Поиск 3 самых прибыльных фильмов за неделю (оптимизированный)" => "
        WITH weekly_sessions AS (
            SELECT id FROM Session 
            WHERE start_time BETWEEN NOW() - INTERVAL '7 days' AND NOW()
        )
        SELECT m.title, SUM(t.price) AS total_revenue
        FROM Movie m
        JOIN Session s ON m.id = s.movie_id
        JOIN Ticket t ON s.id = t.session_id
        WHERE s.id IN (SELECT id FROM weekly_sessions)
        GROUP BY m.title
        ORDER BY total_revenue DESC
        LIMIT 3;
    ",
    "5. Сформировать схему зала (оптимизированный)" => "
        SELECT 
            s.row_number, 
            s.seat_number,
            s.type,
            CASE 
                WHEN t.id IS NULL THEN 'Свободно' 
                ELSE 'Занято' 
            END AS status
        FROM Seat s
        LEFT JOIN Ticket t ON s.id = t.seat_id AND t.session_id = 1
        WHERE s.hall_id = (SELECT hall_id FROM Session WHERE id = 1 LIMIT 1)
        ORDER BY s.row_number, s.seat_number;
    ",
    "6. Диапазон цен (оптимизированный)" => "
        SELECT 
            MIN(price) AS min_price, 
            MAX(price) AS max_price
        FROM Ticket
        WHERE session_id = 1
        AND price IS NOT NULL;
    "
];

function analyzeQuery($pdo, $name, $query) {
  echo "==== $name ====\n";
  $explainQuery = "EXPLAIN ANALYZE $query";
  $stmt = $pdo->query($explainQuery);
  $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $maxLength = 0;
  foreach ($plans as $plan) {
    if (isset($plan['QUERY PLAN'])) {
      $length = strlen($plan['QUERY PLAN']);
      if ($length > $maxLength) {
        $maxLength = $length;
      }
    }
  }

  $tableHeader = str_pad("План выполнения", $maxLength);
  echo "$tableHeader\n";
  echo str_repeat("-", $maxLength) . "\n";

  foreach ($plans as $plan) {
    if (isset($plan['QUERY PLAN'])) {
      echo str_pad($plan['QUERY PLAN'], $maxLength) . "\n";
    }
  }

  echo "\n";
}

echo "==================== ОРИГИНАЛЬНЫЕ ЗАПРОСЫ ====================\n";
foreach ($originalQueries as $name => $query) {
  analyzeQuery($pdo, $name, $query);
}

echo "==================== ОПТИМИЗИРОВАННЫЕ ЗАПРОСЫ ====================\n";
foreach ($optimizedQueries as $name => $query) {
  analyzeQuery($pdo, $name, $query);
}

echo "==================== САМЫЕ БОЛЬШИЕ ОБЪЕКТЫ БД ====================\n";
$sizeQuery = "
    SELECT nspname || '.' || relname AS \"relation\",
           pg_size_pretty(pg_total_relation_size(C.oid)) AS \"total_size\"
    FROM pg_class C
    LEFT JOIN pg_namespace N ON (N.oid = C.relnamespace)
    WHERE nspname NOT IN ('pg_catalog', 'information_schema')
    ORDER BY pg_total_relation_size(C.oid) DESC
    LIMIT 15;
";

$stmt = $pdo->query($sizeQuery);
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo str_pad("Таблица", 40) . " | Размер\n";
echo str_repeat("-", 40) . " | " . str_repeat("-", 20) . "\n";
foreach ($sizes as $size) {
  echo str_pad($size['relation'], 40) . " | " . $size['total_size'] . "\n";
}

echo "\n==================== ИСПОЛЬЗОВАНИЕ ИНДЕКСОВ ====================\n";
$indexUsageQuery = "
    SELECT relname, idx_scan, idx_tup_read, idx_tup_fetch
    FROM pg_stat_user_indexes
    ORDER BY idx_scan DESC
    LIMIT 5;
";

$stmt = $pdo->query($indexUsageQuery);
$indexUsage = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Самые часто используемые индексы:\n";
echo str_pad("Таблица", 20) . " | " . str_pad("Сканирований", 12) . " | " . str_pad("Чтений", 12) . " | " . str_pad("Выборок", 12) . "\n";
echo str_repeat("-", 20) . " | " . str_repeat("-", 12) . " | " . str_repeat("-", 12) . " | " . str_repeat("-", 12) . "\n";
foreach ($indexUsage as $usage) {
  echo str_pad($usage['relname'], 20) . " | " .
      str_pad($usage['idx_scan'], 12) . " | " .
      str_pad($usage['idx_tup_read'], 12) . " | " .
      str_pad($usage['idx_tup_fetch'], 12) . "\n";
}