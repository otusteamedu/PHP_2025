<?php

namespace Igor\Test\Gateway;

use Igor\Test\Collection\OrderCollection;
use Igor\Test\Database\Connection;
use Igor\Test\LazyLoader\LazyLoaderInterface;
use Igor\Test\Model\Order;
use PDO;
use PDOException;

/**
 * Gateway для работы с таблицей orders
 * Управляет операциями CRUD и массовым получением данных
 */
class OrderGateway
{
    private PDO $db;
    private string $tableName = 'orders';
    private ?LazyLoaderInterface $userLazyLoader = null;

    public function __construct(?PDO $db = null, ?LazyLoaderInterface $userLazyLoader = null)
    {
        $this->db = $db ?? Connection::getInstance();
        $this->userLazyLoader = $userLazyLoader;
    }

    /**
     * Установка lazy loader для пользователей
     *
     * @param LazyLoaderInterface $loader
     */
    public function setUserLazyLoader(LazyLoaderInterface $loader): void
    {
        $this->userLazyLoader = $loader;
    }

    /**
     * Поиск заказа по ID
     *
     * @param int $id
     * @return Order|null
     */
    public function findById(int $id): ?Order
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if ($data === false) {
            return null;
        }

        $order = new Order($data);
        
        // Настройка Lazy Load для пользователя
        if ($this->userLazyLoader !== null) {
            $order->setUserLazyLoader($this->userLazyLoader);
        }

        return $order;
    }

    /**
     * Получение всех заказов (массовое получение)
     *
     * @param array $conditions Условия WHERE (опционально)
     * @param string $orderBy Поле для сортировки (опционально)
     * @param int|null $limit Лимит записей (опционально)
     * @return OrderCollection
     */
    public function findAll(array $conditions = [], string $orderBy = '', ?int $limit = null): OrderCollection
    {
        $sql = "SELECT * FROM {$this->tableName}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $orders = [];
        foreach ($rows as $row) {
            $order = new Order($row);
            
            // Настройка Lazy Load для пользователя
            if ($this->userLazyLoader !== null) {
                $order->setUserLazyLoader($this->userLazyLoader);
            }
            
            $orders[] = $order;
        }

        return new OrderCollection($orders);
    }

    /**
     * Вставка нового заказа
     *
     * @param Order $order
     * @return bool
     * @throws PDOException
     */
    public function insert(Order $order): bool
    {
        if (!$order->isNew()) {
            throw new \RuntimeException("Cannot insert existing order. Use update() instead.");
        }

        $sql = "INSERT INTO {$this->tableName} (user_id, total, status) VALUES (:user_id, :total, :status)";
        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            'user_id' => $order->getUserId(),
            'total' => $order->getTotal(),
            'status' => $order->getStatus(),
        ]);

        if ($result) {
            $order->setId((int)$this->db->lastInsertId());
            $order->markAsSaved();
        }

        return $result;
    }

    /**
     * Обновление заказа
     *
     * @param Order $order
     * @return bool
     * @throws PDOException
     */
    public function update(Order $order): bool
    {
        if ($order->isNew()) {
            throw new \RuntimeException("Cannot update new order. Use insert() instead.");
        }

        if (!$order->isDirty()) {
            return true; // Нет изменений для сохранения
        }

        $id = $order->getId();

        $selectStmt = $this->db->prepare("SELECT user_id, total, status FROM {$this->tableName} WHERE id = :id");
        $selectStmt->execute(['id' => $id]);
        $currentData = $selectStmt->fetch(PDO::FETCH_ASSOC);

        if ($currentData === false) {
            throw new \RuntimeException("Cannot update order: order not found.");
        }

        $changedFields = [];

        if ((int)$currentData['user_id'] !== $order->getUserId()) {
            $changedFields['user_id'] = $order->getUserId();
        }

        if ((float)$currentData['total'] !== $order->getTotal()) {
            $changedFields['total'] = $order->getTotal();
        }

        if ((string)$currentData['status'] !== $order->getStatus()) {
            $changedFields['status'] = $order->getStatus();
        }

        if (empty($changedFields)) {
            $order->markAsSaved();
            return true;
        }

        $setParts = [];
        foreach (array_keys($changedFields) as $field) {
            $setParts[] = "{$field} = :{$field}";
        }

        $sql = "UPDATE {$this->tableName} SET " . implode(', ', $setParts) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $params = $changedFields;
        $params['id'] = $id;

        $result = $stmt->execute($params);

        if ($result) {
            $order->markAsSaved();
        }

        return $result;
    }

    /**
     * Сохранение заказа (insert или update в зависимости от состояния)
     *
     * @param Order $order
     * @return bool
     */
    public function save(Order $order): bool
    {
        if ($order->isNew()) {
            return $this->insert($order);
        } else {
            return $this->update($order);
        }
    }

    /**
     * Удаление заказа
     *
     * @param Order $order
     * @return bool
     * @throws PDOException
     */
    public function delete(Order $order): bool
    {
        if ($order->isNew()) {
            throw new \RuntimeException("Cannot delete new order.");
        }

        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $order->getId()]);
    }

    /**
     * Удаление заказа по ID
     *
     * @param int $id
     * @return bool
     */
    public function deleteById(int $id): bool
    {
        $sql = "DELETE FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Подсчет количества заказов
     *
     * @param array $conditions Условия WHERE (опционально)
     * @return int
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->tableName}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return (int)($result['count'] ?? 0);
    }
}
