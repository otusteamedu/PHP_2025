<?php

namespace Igor\Test\LazyLoader;

use Igor\Test\Database\Connection;
use Igor\Test\Model\User;
use PDO;
use PDOException;

/**
 * Lazy Loader для загрузки пользователей
 * Загружает пользователя только при первом обращении
 */
class UserLazyLoader implements LazyLoaderInterface
{
    private PDO $db;
    private string $tableName = 'users';

    /**
     * Кэш загруженных пользователей (опциональный Identity Map)
     *
     * @var User[]
     */
    private array $cache = [];

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Connection::getInstance();
    }

    /**
     * Загрузка пользователя по ID (Lazy Load)
     *
     * @param int $id
     * @return User|null
     */
    public function load(int $id): ?User
    {
        // Проверка кэша (простой Identity Map)
        if (isset($this->cache[$id])) {
            return $this->cache[$id];
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch();

            if ($data === false) {
                return null;
            }

            $user = new User($data);
            
            // Сохранение в кэш
            $this->cache[$id] = $user;

            return $user;
        } catch (PDOException $e) {
            throw new \RuntimeException("Failed to load user with id {$id}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Очистка кэша
     */
    public function clearCache(): void
    {
        $this->cache = [];
    }

    /**
     * Предзагрузка нескольких пользователей
     *
     * @param array $ids Массив идентификаторов
     * @return User[]
     */
    public function loadMultiple(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        // Фильтруем уже загруженные из кэша
        $idsToLoad = [];
        $result = [];

        foreach ($ids as $id) {
            if (isset($this->cache[$id])) {
                $result[$id] = $this->cache[$id];
            } else {
                $idsToLoad[] = $id;
            }
        }

        if (empty($idsToLoad)) {
            return $result;
        }

        // Загружаем оставшихся пользователей одним запросом
        $placeholders = implode(',', array_fill(0, count($idsToLoad), '?'));
        $sql = "SELECT * FROM {$this->tableName} WHERE id IN ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($idsToLoad);
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
            $user = new User($row);
            $this->cache[$user->getId()] = $user;
            $result[$user->getId()] = $user;
        }

        return $result;
    }
}
