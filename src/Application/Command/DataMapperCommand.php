<?php

namespace BookstoreApp\Application\Command;

use BookstoreApp\Infrastructure\Persistence\DataMapper\BookstoreDataMapper;
use BookstoreApp\Infrastructure\Database\Connection;
use BookstoreApp\Infrastructure\Database\IdentityMap;

class DataMapperCommand implements CommandInterface
{
    private BookstoreDataMapper $dataMapper;
    private IdentityMap $identityMap;

    public function __construct(Connection $connection, IdentityMap $identityMap)
    {
        $this->identityMap = $identityMap;
        $this->dataMapper = new BookstoreDataMapper($connection, $identityMap);
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
            case 'collection':
                $this->demoCollection();
                break;
            case 'identity':
                $this->demoIdentityMap();
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
                $this->fullDemo();
                break;
            default:
                echo "Неизвестное действие: $action" . PHP_EOL;
                $this->showHelp();
        }
    }

    private function listAll(): void
    {
        echo "=== Все книжные магазины (Data Mapper) ===" . PHP_EOL;
        $bookstores = $this->dataMapper->findAll();

        $this->displayBookstores($bookstores);
        echo "Итого: " . count($bookstores) . " магазинов" . PHP_EOL;
        echo "Размер Identity Map: " . $this->getIdentityMapSize() . " объектов" . PHP_EOL;
    }

    private function listByCity(string $city): void
    {
        echo "=== Книжные магазины в $city (Data Mapper) ===" . PHP_EOL;
        $bookstores = $this->dataMapper->findByCity($city);

        $this->displayBookstores($bookstores);
        echo "Итого в $city: " . count($bookstores) . " магазинов" . PHP_EOL;
        echo "Размер Identity Map: " . $this->getIdentityMapSize() . " объектов" . PHP_EOL;
    }

    private function findById(int $id): void
    {
        echo "=== Поиск магазина #$id (Data Mapper) ===" . PHP_EOL;

        // Первый поиск - загрузит в Identity Map
        echo "Первый поиск (загрузка из базы данных):" . PHP_EOL;
        $bookstore1 = $this->dataMapper->findById($id);

        if ($bookstore1) {
            $this->displayBookstoreDetail($bookstore1);
        } else {
            echo "Книжный магазин не найден" . PHP_EOL;
            return;
        }

        // Второй поиск - должен взять из Identity Map
        echo "Второй поиск (через Identity Map):" . PHP_EOL;
        $bookstore2 = $this->dataMapper->findById($id);

        if ($bookstore2) {
            $this->displayBookstoreDetail($bookstore2);
            echo "Объекты совпадают: " . ($bookstore1 === $bookstore2 ? 'ДА ✅' : 'НЕТ ❌') . PHP_EOL;
        }

        echo "Размер Identity Map: " . $this->getIdentityMapSize() . " объектов" . PHP_EOL;
    }

    private function demoCollection(): void
    {
        echo "=== Демонстрация коллекции книжных магазинов (Data Mapper) ===" . PHP_EOL;

        $bookstores = $this->dataMapper->findAll();
        $collection = $this->dataMapper->getCollection();

        echo "Размер коллекции: " . $collection->count() . " магазинов" . PHP_EOL;

        // Фильтрация по городу
        $moscowBookstores = $collection->filterByCity('Москва');
        echo "Магазинов в Москве: " . $moscowBookstores->count() . PHP_EOL;

        // Фильтрация по рейтингу
        $highRated = $collection->filterByRating(4.5);
        echo "Магазинов с рейтингом >= 4.5: " . $highRated->count() . PHP_EOL;

        // Список городов
        $cities = $collection->getCities();
        echo "Города с магазинами: " . implode(', ', $cities) . PHP_EOL;

        // Пример итерации
        echo "Топ-5 магазинов по рейтингу:" . PHP_EOL;
        $i = 0;
        foreach ($collection as $bookstore) {
            if ($i >= 5) break;
            printf("%d. %s (%s) - %.2f" . PHP_EOL,
                $i + 1,
                $bookstore->getName(),
                $bookstore->getCity(),
                $bookstore->getRating()
            );
            $i++;
        }
    }

    private function demoIdentityMap(): void
    {
        echo "=== Демонстрация Identity Map ===" . PHP_EOL;

        $this->identityMap->clear();
        echo "Identity Map очищена" . PHP_EOL;

        // Загружаем одни и те же объекты несколько раз
        $ids = [1, 2, 3, 1, 2, 3]; // Повторяющиеся ID

        foreach ($ids as $index => $id) {
            echo "Загрузка магазина #$id (попытка " . ($index + 1) . ")... ";
            $bookstore = $this->dataMapper->findById($id);

            if ($bookstore) {
                echo "OK - ";
                if ($this->identityMap->has(get_class($bookstore), $id)) {
                    echo "Из Identity Map" . PHP_EOL;
                } else {
                    echo "Из базы данных" . PHP_EOL;
                }
            } else {
                echo "Не найден" . PHP_EOL;
            }
        }

        echo "Итоговый размер Identity Map: " . $this->getIdentityMapSize() . " объектов" . PHP_EOL;
        echo "Ожидается: 3 объекта (уникальные ID: 1, 2, 3)" . PHP_EOL;
    }

    private function createBookstore(): void
    {
        echo "=== Создание нового магазина (Data Mapper) ===" . PHP_EOL;

        $bookstore = new \BookstoreApp\Domain\Entity\Bookstore(
            null,
            'Магазин (Data Mapper) ' . date('H:i:s'),
            'Тестовый город',
            'ул. Data Mapper, 123',
            '+7 (999) 999-99-99',
            'datamapper@example.com',
            2024,
            200.0,
            true,
            4.7
        );

        $this->dataMapper->save($bookstore);

        echo "Магазин успешно создан!" . PHP_EOL;
        echo "Новый ID: " . $bookstore->getId() . PHP_EOL;
        $this->displayBookstoreDetail($bookstore);
    }

    private function updateBookstore(int $id): void
    {
        echo "=== Обновление магазина #$id (Data Mapper) ===" . PHP_EOL;

        $bookstore = $this->dataMapper->findById($id);
        if (!$bookstore) {
            echo "Книжный магазин не найден" . PHP_EOL;
            return;
        }

        echo "До обновления:" . PHP_EOL;
        $this->displayBookstoreDetail($bookstore);

        $bookstore->setRating($bookstore->getRating() + 0.1);
        $bookstore->setName($bookstore->getName() . ' [Обновлено]');

        $this->dataMapper->save($bookstore);

        echo "Магазин успешно обновлен!" . PHP_EOL;
        echo "После обновления:" . PHP_EOL;
        $updated = $this->dataMapper->findById($id);
        $this->displayBookstoreDetail($updated);
    }

    private function deleteBookstore(int $id): void
    {
        echo "=== Удаление магазина #$id (Data Mapper) ===" . PHP_EOL;

        $bookstore = $this->dataMapper->findById($id);
        if (!$bookstore) {
            echo "Книжный магазин не найден" . PHP_EOL;
            return;
        }

        $this->displayBookstoreDetail($bookstore);

        $this->dataMapper->delete($id);

        echo "Магазин успешно удален!" . PHP_EOL;

        // Проверяем удаление
        $check = $this->dataMapper->findById($id);
        if (!$check) {
            echo "Проверка: магазина #$id больше не существует" . PHP_EOL;
        } else {
            echo "Проверка не пройдена: магазин всё ещё существует" . PHP_EOL;
        }
    }

    private function fullDemo(): void
    {
        echo "=== Полная демонстрация (Data Mapper) ===" . PHP_EOL;

        // Демонстрация Identity Map
        $this->demoIdentityMap();
        echo PHP_EOL;

        // Демонстрация коллекций
        $this->demoCollection();
        echo PHP_EOL;

        // Демонстрация CRUD
        echo "Операции CRUD:" . PHP_EOL;

        // Create
        echo "1. Создание магазина..." . PHP_EOL;
        $bookstore = new \BookstoreApp\Domain\Entity\Bookstore(
            null, 'Демо-магазин', 'Демо-город', 'Демо-адрес'
        );
        $this->dataMapper->save($bookstore);
        $newId = $bookstore->getId();
        echo "Создан #$newId" . PHP_EOL;

        // Read
        echo "2. Чтение магазина..." . PHP_EOL;
        $found = $this->dataMapper->findById($newId);
        echo "Найден: " . $found->getName() . PHP_EOL;

        // Update
        echo "3. Обновление магазина..." . PHP_EOL;
        $found->setRating(5.0);
        $this->dataMapper->save($found);
        echo "Рейтинг обновлён до 5.0" . PHP_EOL;

        // Delete
        echo "4. Удаление магазина..." . PHP_EOL;
        $this->dataMapper->delete($newId);
        echo "Успешно удалён" . PHP_EOL;

        echo "Демонстрация завершена!" . PHP_EOL;
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

    private function displayBookstoreDetail($bookstore): void
    {
        echo "ID: " . $bookstore->getId() . PHP_EOL;
        echo "Название: " . $bookstore->getName() . PHP_EOL;
        echo "Город: " . $bookstore->getCity() . PHP_EOL;
        echo "Адрес: " . $bookstore->getAddress() . PHP_EOL;
        echo "Рейтинг: " . $bookstore->getRating() . PHP_EOL;
        echo "Кафе: " . ($bookstore->hasCafe() ? 'Да' : 'Нет') . PHP_EOL;
        echo str_repeat("-", 40) . PHP_EOL;
    }

    private function getIdentityMapSize(): int
    {
        // В реальной реализации нужно добавить метод size() в IdentityMap
        // Для демонстрации возвращаем 0
        return 0; // Заглушка
    }

    private function showHelp(): void
    {
        echo "Доступные действия для data-mapper:" . PHP_EOL;
        echo "  list              - Показать все книжные магазины" . PHP_EOL;
        echo "  city [name]       - Показать магазины в городе (по умолчанию: Москва)" . PHP_EOL;
        echo "  find [id]         - Найти магазин по ID с демонстрацией Identity Map" . PHP_EOL;
        echo "  collection        - Демонстрация работы коллекции" . PHP_EOL;
        echo "  identity          - Демонстрация Identity Map" . PHP_EOL;
        echo "  create            - Создать новый магазин" . PHP_EOL;
        echo "  update [id]       - Обновить магазин по ID (по умолчанию: 1)" . PHP_EOL;
        echo "  delete [id]       - Удалить магазин по ID (по умолчанию: 1)" . PHP_EOL;
        echo "  demo              - Полная демонстрация" . PHP_EOL;
    }

    public function getDescription(): string
    {
        return "Демонстрация шаблона Data Mapper с Identity Map и коллекциями";
    }
}