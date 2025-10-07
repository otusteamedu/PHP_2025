<?php

namespace BookstoreApp\Application\Command;

use BookstoreApp\Infrastructure\Persistence\ActiveRecord\BookstoreActiveRecord;
use BookstoreApp\Infrastructure\Database\Connection;

class ActiveRecordCommand implements CommandInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        // Инициализация статического подключения для Active Record
        BookstoreActiveRecord::initConnection($connection);
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
            case 'find':
                $id = isset($args[1]) ? (int)$args[1] : 1;
                $this->findById($id);
                break;
            case 'create':
                $this->createBookstore();
                break;
            case 'update':
                $id = isset($args[1]) ? (int)$args[1] : 1;
                $this->updateBookstore($id);
                break;
            case 'delete':
                $id = isset($args[1]) ? (int)$args[1] : 1;
                $this->deleteBookstore($id);
                break;
            case 'demo':
                $this->demoCRUD();
                break;
            default:
                echo "Неизвестное действие: $action" . PHP_EOL;
                $this->showHelp();
        }
    }

    private function listAll(): void
    {
        $count = BookstoreActiveRecord::countAll();
        echo "=== Все книжные магазины (Active Record) ===" . PHP_EOL;
        echo "Всего магазинов: $count" . PHP_EOL;
        $bookstores = BookstoreActiveRecord::findAllPaginated(1000);
        $this->displayBookstores($bookstores);
        echo "Показано " . min($count, 1000) . " из " . $count;
    }

    private function listByCity(string $city): void
    {
        echo "=== Книжные магазины в $city (Active Record) ===" . PHP_EOL;
        $bookstores = BookstoreActiveRecord::findByCity($city);

        $this->displayBookstores($bookstores);
        echo "Итого в $city: " . count($bookstores) . " магазинов" . PHP_EOL;
    }

    private function findById(int $id): void
    {
        echo "=== Поиск магазина #$id (Active Record) ===" . PHP_EOL;
        $bookstore = BookstoreActiveRecord::find($id);

        if ($bookstore) {
            $this->displayBookstoreDetail($bookstore);
        } else {
            echo "Книжный магазин не найден" . PHP_EOL;
        }
    }

    private function createBookstore(): void
    {
        echo "=== Создание нового магазина (Active Record) ===" . PHP_EOL;

        $bookstore = new BookstoreActiveRecord();
        $bookstore->setName('Новый книжный магазин ' . date('Y-m-d H:i'));
        $bookstore->setCity('Тестовый город');
        $bookstore->setAddress('ул. Тестовая, 123');
        $bookstore->setPhone('+7 (999) 123-45-67');
        $bookstore->setEmail('test@example.com');
        $bookstore->setEstablishedYear(2024);
        $bookstore->setSquareMeters(150.5);
        $bookstore->setHasCafe(true);
        $bookstore->setRating(4.2);

        if ($bookstore->save()) {
            echo "Магазин успешно создан!" . PHP_EOL;
            $this->displayBookstoreDetail($bookstore);
        } else {
            echo "Не удалось создать магазин" . PHP_EOL;
        }
    }

    private function updateBookstore(int $id): void
    {
        echo "=== Обновление магазина #$id (Active Record) ===" . PHP_EOL;

        $bookstore = BookstoreActiveRecord::find($id);
        if (!$bookstore) {
            echo "Книжный магазин не найден" . PHP_EOL;
            return;
        }

        echo "До обновления:" . PHP_EOL;
        $this->displayBookstoreDetail($bookstore);

        $bookstore->setRating($bookstore->getRating() + 0.1);
        $bookstore->setName($bookstore->getName() . ' (обновлено)');

        if ($bookstore->save()) {
            echo "Магазин успешно обновлен!" . PHP_EOL;
            echo "После обновления:" . PHP_EOL;
            $this->displayBookstoreDetail(BookstoreActiveRecord::find($id));
        } else {
            echo "Не удалось обновить магазин" . PHP_EOL;
        }
    }

    private function deleteBookstore(int $id): void
    {
        echo "=== Удаление магазина #$id (Active Record) ===" . PHP_EOL;

        $bookstore = BookstoreActiveRecord::find($id);
        if (!$bookstore) {
            echo "Книжный магазин не найден" . PHP_EOL;
            return;
        }

        $this->displayBookstoreDetail($bookstore);

        if ($bookstore->delete()) {
            echo "Магазин успешно удален!" . PHP_EOL;

            // Проверяем, что удаление прошло успешно
            $check = BookstoreActiveRecord::find($id);
            if (!$check) {
                echo "Проверка: магазина #$id больше не существует" . PHP_EOL;
            }
        } else {
            echo "Не удалось удалить магазин" . PHP_EOL;
        }
    }

    private function demoCRUD(): void
    {
        echo "=== Демонстрация CRUD (Active Record) ===" . PHP_EOL;

        // Create
        echo "1. Создание нового магазина..." . PHP_EOL;
        $bookstore = new BookstoreActiveRecord();
        $bookstore->setName('Демо-магазин ' . time());
        $bookstore->setCity('Демо-город');
        $bookstore->setAddress('Демо-адрес');
        $bookstore->setRating(4.5);
        $bookstore->setHasCafe(false);

        if ($bookstore->save()) {
            $id = $bookstore->getId();
            echo "Создан магазин #$id" . PHP_EOL;

            // Read
            echo "2. Чтение магазина #$id..." . PHP_EOL;
            $found = BookstoreActiveRecord::find($id);
            if ($found) {
                echo "Найден магазин: " . $found->getName() . PHP_EOL;

                // Update
                echo "3. Обновление магазина #$id..." . PHP_EOL;
                $found->setRating(4.8);
                if ($found->save()) {
                    echo "Рейтинг обновлён до 4.8" . PHP_EOL;

                    // Delete
                    echo "4. Удаление магазина #$id..." . PHP_EOL;
                    if ($found->delete()) {
                        echo "Успешно удалён" . PHP_EOL;
                    } else {
                        echo "Не удалось удалить" . PHP_EOL;
                    }
                } else {
                    echo "Не удалось обновить" . PHP_EOL;
                }
            } else {
                echo "Не удалось прочитать" . PHP_EOL;
            }
        } else {
            echo "Не удалось создать" . PHP_EOL;
        }
    }

    private function displayBookstores(array $bookstores): void
    {
        foreach ($bookstores as $bookstore) {
            $cafe = $bookstore->hasCafe() ? ' кафе ' : '';
            printf("%3d: %-25s %-15s %4.2f %s" . PHP_EOL,
                $bookstore->getId(),
                substr($bookstore->getName(), 0, 24),
                $bookstore->getCity(),
                $bookstore->getRating(),
                $cafe
            );
        }
    }

    private function displayBookstoreDetail(BookstoreActiveRecord $bookstore): void
    {
        echo "ID: " . $bookstore->getId() . PHP_EOL;
        echo "Название: " . $bookstore->getName() . PHP_EOL;
        echo "Город: " . $bookstore->getCity() . PHP_EOL;
        echo "Адрес: " . $bookstore->getAddress() . PHP_EOL;
        echo "Телефон: " . ($bookstore->getPhone() ?: 'нет данных') . PHP_EOL;
        echo "Электронная почта: " . ($bookstore->getEmail() ?: 'нет данных') . PHP_EOL;
        echo "Основан: " . ($bookstore->getEstablishedYear() ?: 'нет данных') . PHP_EOL;
        echo "Площадь: " . ($bookstore->getSquareMeters() ? $bookstore->getSquareMeters() . ' м²' : 'нет данных') . PHP_EOL;
        echo "Кафе: " . ($bookstore->hasCafe() ? 'Да' : 'Нет') . PHP_EOL;
        echo "Рейтинг: " . $bookstore->getRating() . PHP_EOL;
        echo "Создано: " . $bookstore->getCreatedAt() . PHP_EOL;
        echo "Обновлено: " . $bookstore->getUpdatedAt() . PHP_EOL;
        echo str_repeat("-", 50) . PHP_EOL;
    }

    private function showHelp(): void
    {
        echo "Доступные действия для active-record:" . PHP_EOL;
        echo "  list              - Показать все книжные магазины" . PHP_EOL;
        echo "  city [name]       - Показать магазины в городе (по умолчанию: Москва)" . PHP_EOL;
        echo "  find [id]         - Найти магазин по ID (по умолчанию: 1)" . PHP_EOL;
        echo "  create            - Создать новый магазин" . PHP_EOL;
        echo "  update [id]       - Обновить магазин по ID (по умолчанию: 1)" . PHP_EOL;
        echo "  delete [id]       - Удалить магазин по ID (по умолчанию: 1)" . PHP_EOL;
        echo "  demo              - Полная демонстрация CRUD" . PHP_EOL;
    }

    public function getDescription(): string
    {
        return "Демонстрация шаблона Active Record с операциями CRUD";
    }
}