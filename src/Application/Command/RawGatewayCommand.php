<?php

namespace BookstoreApp\Application\Command;

use BookstoreApp\Infrastructure\Persistence\RawDataGateway\BookstoreRawGateway;
use BookstoreApp\Infrastructure\Database\Connection;

class RawGatewayCommand implements CommandInterface
{
    private BookstoreRawGateway $gateway;

    public function __construct(Connection $connection)
    {
        $this->gateway = new BookstoreRawGateway($connection);
    }

    public function execute(array $args = []): void
    {
        $action = $args[0] ?? 'list';

        switch ($action) {
            case 'list':
                $this->listAll();
                break;
            case 'rating':
                $minRating = isset($args[1]) ? (float)$args[1] : 4.0;
                $this->listByRating($minRating);
                break;
            case 'cafe':
                $this->listWithCafe();
                break;
            case 'cities':
                $this->listCities();
                break;
            case 'stats':
                $this->showStatistics();
                break;
            default:
                echo "Неизвестное действие: $action" . PHP_EOL;
                $this->showHelp();
        }
    }

    private function listAll(): void
    {
        echo "=== Все книжные магазины (Raw Data Gateway) ===" . PHP_EOL;
        $bookstores = $this->gateway->fetchAllBookstores();

        $this->displayBookstores($bookstores);
        echo "Итого: " . count($bookstores) . " магазинов" . PHP_EOL;
    }

    private function listByRating(float $minRating): void
    {
        echo "=== Книжные магазины с рейтингом >= $minRating (Raw Data Gateway) ===" . PHP_EOL;
        $bookstores = $this->gateway->fetchBookstoresByRating($minRating);

        $this->displayBookstores($bookstores);
        echo "Итого с рейтингом >= $minRating: " . count($bookstores) . " магазинов" . PHP_EOL;
    }

    private function listWithCafe(): void
    {
        echo "=== Книжные магазины с кафе (Raw Data Gateway) ===" . PHP_EOL;
        $bookstores = $this->gateway->fetchBookstoresWithCafe();

        $this->displayBookstores($bookstores);
        echo "Итого с кафе: " . count($bookstores) . " магазинов" . PHP_EOL;
    }

    private function listCities(): void
    {
        echo "=== Cities with Bookstores (Raw Data Gateway) ===" . PHP_EOL;
        $cities = $this->gateway->getCitiesWithBookstores();

        foreach ($cities as $city) {
            echo "- $city" . PHP_EOL;
        }

        echo "Total cities: " . count($cities) . PHP_EOL;
    }

    private function showStatistics(): void
    {
        echo "=== Bookstore Statistics by City (Raw Data Gateway) ===" . PHP_EOL;
        $stats = $this->gateway->getAverageRatingByCity();

        printf("%-20s %-10s %-8s" . PHP_EOL, "City", "Avg Rating", "Count");
        echo str_repeat("-", 40) . PHP_EOL;

        foreach ($stats as $stat) {
            printf("%-20s %-10.2f %-8d" . PHP_EOL,
                $stat['city'],
                $stat['avg_rating'],
                $stat['count']
            );
        }
    }

    private function displayBookstores(array $bookstores): void
    {
        foreach ($bookstores as $bookstore) {
            $cafe = $bookstore['has_cafe'] ? ' cafe ' : '';
            printf("%3d: %-35s %-15s %4.2f %s" . PHP_EOL,
                $bookstore['id'],
                substr($bookstore['name'], 0, 34),
                $bookstore['city'],
                $bookstore['rating'],
                $cafe
            );
        }
    }

    private function showHelp(): void
    {
        echo "Available actions for raw-gateway:" . PHP_EOL;
        echo "  list          - List all bookstores" . PHP_EOL;
        echo "  rating [min]  - List bookstores with minimum rating (default: 4.0)" . PHP_EOL;
        echo "  cafe          - List bookstores with cafe" . PHP_EOL;
        echo "  cities        - List all cities with bookstores" . PHP_EOL;
        echo "  stats         - Show statistics by city" . PHP_EOL;
    }

    public function getDescription(): string
    {
        return "Raw Data Gateway pattern demonstration with direct SQL queries";
    }
}