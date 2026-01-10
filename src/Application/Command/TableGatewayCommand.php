<?php

namespace BookstoreApp\Application\Command;

use BookstoreApp\Infrastructure\Persistence\TableDataGateway\BookstoreGateway;
use BookstoreApp\Infrastructure\Database\Connection;

class TableGatewayCommand implements CommandInterface
{
    private BookstoreGateway $gateway;

    public function __construct(Connection $connection)
    {
        $this->gateway = new BookstoreGateway($connection);
    }

    public function execute(array $args = []): void
    {
        $action = $args[0] ?? 'list';

        switch ($action) {
            case 'list':
                $this->listAll();
                break;
            case 'city':
                $city = $args[1] ?? 'Москва';
                $this->listByCity($city);
                break;
            default:
                echo "Unknown action: $action" . PHP_EOL;
        }
    }

    private function listAll(): void
    {
        echo "=== All Bookstores (Table Data Gateway) ===" . PHP_EOL;
        $bookstores = $this->gateway->findAll();

        foreach ($bookstores as $bookstore) {
            printf("%d: %s (%s) - Rating: %.2f" . PHP_EOL,
                $bookstore['id'],
                $bookstore['name'],
                $bookstore['city'],
                $bookstore['rating']
            );
        }

        echo "Total: " . count($bookstores) . " bookstores" . PHP_EOL;
    }

    private function listByCity(string $city): void
    {
        echo "=== Bookstores in $city (Table Data Gateway) ===" . PHP_EOL;
        $bookstores = $this->gateway->findByCity($city);

        foreach ($bookstores as $bookstore) {
            printf("%d: %s - %s, Rating: %.2f" . PHP_EOL,
                $bookstore['id'],
                $bookstore['name'],
                $bookstore['address'],
                $bookstore['rating']
            );
        }

        echo "Total in $city: " . count($bookstores) . " bookstores" . PHP_EOL;
    }

    public function getDescription(): string
    {
        return "Table Data Gateway pattern demonstration";
    }
}