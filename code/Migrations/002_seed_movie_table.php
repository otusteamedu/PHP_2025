<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database\PostgresConnection;

$migrationName = '002_seed_movie_table';

$movies = [
    ['title' => 'The First Movie', 'duration' => 120, 'description' => 'Test movie #1'],
    ['title' => 'Midnight Run', 'duration' => 110, 'description' => 'Test movie #2'],
    ['title' => 'Ocean Breeze', 'duration' => 98, 'description' => 'Test movie #3'],
    ['title' => 'Silent Streets', 'duration' => 105, 'description' => 'Test movie #4'],
    ['title' => 'Fading Lights', 'duration' => 134, 'description' => 'Test movie #5'],
    ['title' => 'Broken Compass', 'duration' => 101, 'description' => 'Test movie #6'],
    ['title' => 'Echoes', 'duration' => 95, 'description' => 'Test movie #7'],
    ['title' => 'Last Harbor', 'duration' => 123, 'description' => 'Test movie #8'],
    ['title' => 'Rising Tide', 'duration' => 112, 'description' => 'Test movie #9'],
    ['title' => 'Hidden Trail', 'duration' => 108, 'description' => 'Test movie #10'],
    ['title' => 'Neon Nights', 'duration' => 99, 'description' => 'Test movie #11'],
    ['title' => 'Crimson Sky', 'duration' => 130, 'description' => 'Test movie #12'],
    ['title' => 'Glass River', 'duration' => 97, 'description' => 'Test movie #13'],
    ['title' => 'Paper Walls', 'duration' => 102, 'description' => 'Test movie #14'],
    ['title' => 'Long Road Home', 'duration' => 118, 'description' => 'Test movie #15'],
];

try {
    $db = new PostgresConnection();
    $inserted = 0;

    foreach ($movies as $movie) {
        $db->insert('movie', $movie);
        $inserted++;
    }

    echo "[OK] {$migrationName} applied, inserted {$inserted} rows" . PHP_EOL;
} catch (\PDOException $e) {
    echo "[ERROR] {$migrationName} failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
