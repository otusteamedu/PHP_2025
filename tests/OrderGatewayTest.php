<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Igor\Test\Collection\OrderCollection;
use Igor\Test\Database\Connection;
use Igor\Test\Gateway\OrderGateway;
use Igor\Test\LazyLoader\UserLazyLoader;
use Igor\Test\Model\Order;
use PDO;

/**
 * Тесты для Row Data Gateway паттерна
 * 
 * ВНИМАНИЕ: Для запуска тестов необходимо:
 * 1. Создать БД и выполнить schema.sql
 * 2. Настроить подключение в config.php или передать параметры в Connection::setConfig()
 */
class OrderGatewayTest
{
    private OrderGateway $gateway;
    private PDO $db;
    private bool $testsPassed = true;

    public function __construct()
    {
        // Настройка тестового подключения
        // В реальном проекте используйте отдельную тестовую БД
        try {
            Connection::setConfig([
                'host' => 'localhost',
                'dbname' => 'test_db',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            ]);

            $this->db = Connection::getInstance();
            $userLazyLoader = new UserLazyLoader();
            $this->gateway = new OrderGateway(null, $userLazyLoader);
        } catch (\Exception $e) {
            echo "ОШИБКА: Не удалось подключиться к БД: " . $e->getMessage() . "\n";
            echo "Убедитесь, что БД создана и schema.sql выполнен.\n";
            exit(1);
        }
    }

    /**
     * Запуск всех тестов
     */
    public function runAllTests(): void
    {
        echo "Запуск тестов для Row Data Gateway\n";
        echo str_repeat("=", 60) . "\n\n";

        $tests = [
            'testFindById' => [$this, 'testFindById'],
            'testFindAll' => [$this, 'testFindAll'],
            'testCollectionIterator' => [$this, 'testCollectionIterator'],
            'testCollectionFilter' => [$this, 'testCollectionFilter'],
            'testInsert' => [$this, 'testInsert'],
            'testUpdate' => [$this, 'testUpdate'],
            'testSave' => [$this, 'testSave'],
            'testDelete' => [$this, 'testDelete'],
            'testCount' => [$this, 'testCount'],
            'testLazyLoad' => [$this, 'testLazyLoad'],
            'testFindAllWithConditions' => [$this, 'testFindAllWithConditions'],
        ];

        $passed = 0;
        $failed = 0;

        foreach ($tests as $testName => $testMethod) {
            try {
                $testMethod();
                echo "✓ {$testName}: PASSED\n";
                $passed++;
            } catch (\Exception $e) {
                echo "✗ {$testName}: FAILED - {$e->getMessage()}\n";
                $failed++;
                $this->testsPassed = false;
            }
        }

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "Результаты: {$passed} пройдено, {$failed} провалено\n";

        if (!$this->testsPassed) {
            exit(1);
        }
    }

    /**
     * Тест: Поиск заказа по ID
     */
    private function testFindById(): void
    {
        $order = $this->gateway->findById(1);
        
        if ($order === null) {
            throw new \Exception("Заказ с ID=1 не найден");
        }

        if ($order->getId() !== 1) {
            throw new \Exception("Неверный ID заказа");
        }

        if ($order->isNew()) {
            throw new \Exception("Заказ должен быть помечен как существующий");
        }
    }

    /**
     * Тест: Массовое получение заказов
     */
    private function testFindAll(): void
    {
        $collection = $this->gateway->findAll();
        
        if (!($collection instanceof OrderCollection)) {
            throw new \Exception("findAll() должен возвращать OrderCollection");
        }

        if ($collection->count() === 0) {
            throw new \Exception("Коллекция не должна быть пустой (проверьте наличие данных в БД)");
        }
    }

    /**
     * Тест: Iterator для коллекции
     */
    private function testCollectionIterator(): void
    {
        $collection = $this->gateway->findAll();
        $count = 0;

        foreach ($collection as $order) {
            if (!($order instanceof Order)) {
                throw new \Exception("Элемент коллекции должен быть Order");
            }
            $count++;
        }

        if ($count !== $collection->count()) {
            throw new \Exception("Количество итераций не совпадает с count()");
        }
    }

    /**
     * Тест: Фильтрация коллекции
     */
    private function testCollectionFilter(): void
    {
        $collection = $this->gateway->findAll();
        $completed = $collection->filter(function (Order $order) {
            return $order->getStatus() === 'completed';
        });

        if (!($completed instanceof OrderCollection)) {
            throw new \Exception("filter() должен возвращать OrderCollection");
        }

        foreach ($completed as $order) {
            if ($order->getStatus() !== 'completed') {
                throw new \Exception("Фильтр работает некорректно");
            }
        }
    }

    /**
     * Тест: Вставка нового заказа
     */
    private function testInsert(): void
    {
        $order = new Order();
        $order->setUserId(1);
        $order->setTotal(500.00);
        $order->setStatus('pending');

        if (!$order->isNew()) {
            throw new \Exception("Новый заказ должен быть помечен как isNew()");
        }

        if (!$this->gateway->insert($order)) {
            throw new \Exception("Не удалось вставить заказ");
        }

        if ($order->getId() === null) {
            throw new \Exception("ID должен быть установлен после вставки");
        }

        if ($order->isNew()) {
            throw new \Exception("Заказ не должен быть помечен как новый после вставки");
        }

        // Удаляем тестовый заказ
        $this->gateway->delete($order);
    }

    /**
     * Тест: Обновление заказа
     */
    private function testUpdate(): void
    {
        $order = $this->gateway->findById(1);
        
        if ($order === null) {
            throw new \Exception("Заказ с ID=1 не найден для теста обновления");
        }

        $originalStatus = $order->getStatus();
        $newStatus = $originalStatus === 'pending' ? 'processing' : 'pending';

        $order->setStatus($newStatus);

        if (!$order->isDirty()) {
            throw new \Exception("Заказ должен быть помечен как измененный");
        }

        if (!$this->gateway->update($order)) {
            throw new \Exception("Не удалось обновить заказ");
        }

        if ($order->isDirty()) {
            throw new \Exception("Заказ не должен быть помечен как измененный после обновления");
        }

        // Восстанавливаем исходное значение
        $order->setStatus($originalStatus);
        $this->gateway->update($order);
    }

    /**
     * Тест: Сохранение заказа (save)
     */
    private function testSave(): void
    {
        // Тест сохранения нового заказа
        $newOrder = new Order();
        $newOrder->setUserId(1);
        $newOrder->setTotal(300.00);
        $newOrder->setStatus('pending');

        if (!$this->gateway->save($newOrder)) {
            throw new \Exception("Не удалось сохранить новый заказ");
        }

        $savedId = $newOrder->getId();

        // Тест сохранения существующего заказа
        $existingOrder = $this->gateway->findById($savedId);
        if ($existingOrder === null) {
            throw new \Exception("Не удалось найти сохраненный заказ");
        }

        $existingOrder->setTotal(350.00);
        if (!$this->gateway->save($existingOrder)) {
            throw new \Exception("Не удалось обновить существующий заказ");
        }

        // Удаляем тестовый заказ
        $this->gateway->delete($existingOrder);
    }

    /**
     * Тест: Удаление заказа
     */
    private function testDelete(): void
    {
        // Создаем тестовый заказ
        $order = new Order();
        $order->setUserId(1);
        $order->setTotal(200.00);
        $order->setStatus('pending');
        $this->gateway->insert($order);
        $id = $order->getId();

        // Удаляем
        if (!$this->gateway->delete($order)) {
            throw new \Exception("Не удалось удалить заказ");
        }

        // Проверяем, что заказ удален
        $deleted = $this->gateway->findById($id);
        if ($deleted !== null) {
            throw new \Exception("Заказ не был удален из БД");
        }
    }

    /**
     * Тест: Подсчет заказов
     */
    private function testCount(): void
    {
        $totalCount = $this->gateway->count();
        
        if ($totalCount < 0) {
            throw new \Exception("Количество не может быть отрицательным");
        }

        $completedCount = $this->gateway->count(['status' => 'completed']);
        
        if ($completedCount > $totalCount) {
            throw new \Exception("Количество завершенных не может быть больше общего");
        }
    }

    /**
     * Тест: Lazy Load пользователя
     */
    private function testLazyLoad(): void
    {
        $order = $this->gateway->findById(1);
        
        if ($order === null) {
            throw new \Exception("Заказ с ID=1 не найден для теста Lazy Load");
        }

        // Пользователь еще не загружен
        if ($order->isUserLoaded()) {
            throw new \Exception("Пользователь не должен быть загружен до первого обращения");
        }

        // Первое обращение - должна произойти загрузка
        $user = $order->getUser();
        
        if ($user === null) {
            throw new \Exception("Пользователь должен быть загружен");
        }

        if (!$order->isUserLoaded()) {
            throw new \Exception("Пользователь должен быть помечен как загруженный");
        }

        // Второе обращение - пользователь должен быть в памяти
        $user2 = $order->getUser();
        
        if ($user !== $user2) {
            throw new \Exception("Повторное обращение должно возвращать тот же объект");
        }
    }

    /**
     * Тест: Поиск с условиями
     */
    private function testFindAllWithConditions(): void
    {
        $userOrders = $this->gateway->findAll(['user_id' => 1]);
        
        foreach ($userOrders as $order) {
            if ($order->getUserId() !== 1) {
                throw new \Exception("Все заказы должны принадлежать пользователю #1");
            }
        }

        $completedOrders = $this->gateway->findAll(['status' => 'completed']);
        
        foreach ($completedOrders as $order) {
            if ($order->getStatus() !== 'completed') {
                throw new \Exception("Все заказы должны иметь статус 'completed'");
            }
        }
    }
}

// Запуск тестов
if (php_sapi_name() === 'cli') {
    $test = new OrderGatewayTest();
    $test->runAllTests();
}
