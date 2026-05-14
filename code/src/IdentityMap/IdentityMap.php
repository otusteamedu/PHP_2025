<?php

namespace App\IdentityMap;

use Memcached;
use RuntimeException;

/**
 * Базовая реализация Identity Map через Memcached.
 *
 * Identity Map хранит уже созданные объекты и помогает не создавать дубликаты
 * для одной и той же строки БД.
 */
abstract class IdentityMap
{
    private Memcached $memcached;

    public function __construct()
    {
        $this->memcached = new Memcached();
        $this->memcached->addServer(
            getenv('MEMCACHED_HOST'),
            (int) getenv('MEMCACHED_PORT')
        );
    }

    public function get(int $id): ?object
    {
        $object = $this->memcached->get($this->getKey($id));

        if ($object === false) {
            return null;
        }

        return $object;
    }

    public function set(object $object): void
    {
        if (!method_exists($object, 'getId')) {
            throw new RuntimeException('Объект ' . $this->getObjectName() . ' должен иметь метод getId().');
        }

        $this->memcached->set($this->getKey($object->getId()), $object, 600);
    }

    public function delete(int $id): void
    {
        $this->memcached->delete($this->getKey($id));
    }

    /**
     * Очищает Identity Map для конкретной таблицы через смену версии ключей.
     */
    public function clear(): void
    {
        $this->memcached->set($this->getVersionKey(), $this->getVersion() + 1, 600);
    }

    /**
     * Возвращает имя таблицы, которое используется как часть ключа кэша.
     */
    abstract protected function getTableName(): string;

    /**
     * Возвращает имя объекта, чтобы базовый класс мог формировать понятные ошибки.
     */
    abstract protected function getObjectName(): string;

    private function getKey(int $id): string
    {
        return $this->getTableName()
            . ':' . $this->getObjectName()
            . ':' . $this->getVersion()
            . ':' . $id;
    }

    /**
     * Возвращает версию ключей Identity Map для таблицы.
     *
     * Версия добавлена, чтобы команда init могла сбросить кэш пользователей
     * после пересоздания таблицы: старые ключи остаются в Memcached, но больше
     * не используются, потому что новые чтения идут по ключам с новой версией.
     */
    private function getVersion(): int
    {
        $version = $this->memcached->get($this->getVersionKey());

        if ($version === false) {
            $this->memcached->set($this->getVersionKey(), 1, 600);
            return 1;
        }

        return (int) $version;
    }

    private function getVersionKey(): string
    {
        return $this->getTableName() . ':' . $this->getObjectName() . ':version';
    }
}
