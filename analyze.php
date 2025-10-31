<?php

function getQuery1(): array
{
    return [
        'description' => 'Выбор всех фильмов на сегодня',
        'sql' => <<<'SQL'
            SELECT DISTINCT m.title
            FROM movie m
            JOIN showtime s ON m.id = s.movie_id
            -- Поправил запрос для возможности использовать индекс
            -- WHERE DATE(s.starts_at) = CURRENT_DATE;
            WHERE CAST((s.starts_at AT TIME ZONE 'UTC') AS date) = CURRENT_DATE
        SQL
    ];
}

function getQuery2(): array
{
    return [
        'description' => 'Подсчёт проданных билетов за неделю',
        'sql' => <<<'SQL'
            SELECT COUNT(*) AS sold_tickets_last_week
            FROM ticket
            WHERE status = 'sold' AND sold_at >= NOW() - INTERVAL '7 days'
        SQL
    ];
}

function getQuery3(): array
{
    return [
        'description' => 'Формирование афиши на сегодня',
        'sql' => <<<'SQL'
            SELECT m.title, s.starts_at, h.name AS hall_name
            FROM showtime s
            JOIN movie m ON s.movie_id = m.id
            JOIN hall h ON s.hall_id = h.id
            -- WHERE DATE(s.starts_at) = CURRENT_DATE;
            WHERE CAST((s.starts_at AT TIME ZONE 'UTC') AS date) = CURRENT_DATE
            ORDER BY s.starts_at
        SQL
    ];
}

function getQuery4(): array
{
    return [
        'description' => 'Поиск 3 самых прибыльных фильмов за неделю',
        'sql' => <<<'SQL'
            SELECT m.title, SUM(t.price_paid) AS total_revenue
            FROM ticket t
            JOIN showtime s ON t.showtime_id = s.id
            JOIN movie m ON s.movie_id = m.id
            WHERE t.status = 'sold' AND t.sold_at >= NOW() - INTERVAL '7 days'
            GROUP BY m.title
            ORDER BY total_revenue DESC
            LIMIT 3
        SQL
    ];
}

function getQuery5(): array
{
    return [
        'description' => 'Схема зала со свободными/занятыми местами для сеанса ID=1',
        'sql' => <<<'SQL'
            SELECT
                s.row_number,
                s.seat_number,
                CASE
                    WHEN t.id IS NULL THEN 'свободно'
                    ELSE 'занято'
                END AS status
            FROM seat s
            LEFT JOIN ticket t ON s.id = t.seat_id AND t.showtime_id = 1
            WHERE s.hall_id = (SELECT hall_id FROM showtime WHERE id = 1)
            ORDER BY s.row_number, s.seat_number
        SQL
    ];
}

function getQuery6(): array
{
    return [
        'description' => 'Диапазон цен на билет для сеанса ID=1',
        'sql' => <<<'SQL'
            SELECT
                MIN(st.base_price + COALESCE(sty.price_modifier, 0)) AS min_price,
                MAX(st.base_price + COALESCE(sty.price_modifier, 0)) AS max_price
            FROM showtime st
            JOIN hall h ON st.hall_id = h.id
            JOIN seat s ON h.id = s.hall_id
            JOIN seat_type sty ON s.seat_type_id = sty.id
            WHERE st.id = 1
        SQL
    ];
}

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
} catch (PDOException $e) {
    exit('Ошибка подключения к БД: '.$e->getMessage());
}

$queryFunctions = [
    'getQuery1', 'getQuery2', 'getQuery3',
    'getQuery4', 'getQuery5', 'getQuery6',
];

$report = '';
$queryCounter = 1;

echo "Начинаем анализ производительности запросов...\n";

foreach ($queryFunctions as $functionName) {
    $queryData = $functionName();
    $description = $queryData['description'];
    $query = $queryData['sql'];

    echo "Анализируем запрос #{$queryCounter}: {$description}\n";

    try {
        $stmt = $pdo->query('EXPLAIN ANALYZE '.$query);
        $plan = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $report .= "======================================================================\n";
        $report .= 'ЗАПРОС #'.$queryCounter.': '.$description."\n";
        $report .= "======================================================================\n\n";
        $report .= "SQL:\n";
        $report .= "----\n";
        $report .= trim($query)."\n\n";
        $report .= "ПЛАН ВЫПОЛНЕНИЯ (EXPLAIN ANALYZE):\n";
        $report .= "-----------------------------------\n";
        $report .= implode("\n", $plan)."\n\n\n";

    } catch (PDOException $e) {
        $report .= "======================================================================\n";
        $report .= 'ОШИБКА при анализе запроса #'.$queryCounter.': '.$description."\n";
        $report .= "======================================================================\n\n";
        $report .= "SQL:\n".trim($query)."\n\n";
        $report .= 'Ошибка: '.$e->getMessage()."\n\n\n";
    }

    $queryCounter++;
}

$reportFile = 'analysis_report.txt';
file_put_contents($reportFile, $report);

echo "\nАнализ завершен. Результаты сохранены в файл: {$reportFile}\n";
