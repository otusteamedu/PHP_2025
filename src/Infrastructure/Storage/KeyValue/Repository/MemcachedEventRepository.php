<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\KeyValue\Repository;

use App\Core\Storage\KeyValue\Memcached\MemcachedDriver;
use App\Domain\EventSystem\Interface\EventRepositoryInterface;
use App\Domain\EventSystem\Model\AddEventModel;
use App\Domain\EventSystem\Model\GetEventModel;
use App\Domain\EventSystem\Model\SearchEventModel;

class MemcachedEventRepository implements EventRepositoryInterface
{
    private readonly \Memcached $memcached;

    public function __construct(MemcachedDriver $memcachedDriver)
    {
        $this->memcached = $memcachedDriver->getHandler();
    }

    public function addEvent(AddEventModel $addEventModel): bool
    {
        $result = $this->memcached->add($addEventModel->getEventKey(), $addEventModel->serialize());

        if ($result === true) {
            $this->memcached->add(
                self::EVENT_CONDITIONS_KEY . ':' . $addEventModel->getPreparedConditions(),
                $addEventModel->getEventKey(),
            );

            return true;
        }

        if ($this->memcached->getResultCode() === $this->memcached::RES_NOTSTORED) {
            throw new \InvalidArgumentException('Событие с такими условиями уже существует.', 400);
        }

        throw new \Exception($this->memcached->getLastErrorMessage() ?: 'Внутренняя ошибка сервиса.', 500);
    }

    public function getEvent(SearchEventModel $searchEventModel): GetEventModel
    {
        $invertedKeys = array_map(
            static fn(string $params) => self::EVENT_CONDITIONS_KEY .":$params",
            $searchEventModel->getPreparedParams(),
        );

        $eventKeys = [];
        foreach ($invertedKeys as $key) {
            $eventKey = $this->memcached->get($key);
            if ($eventKey === false) {
                continue;
            }
            $eventKeys[] = $eventKey;
        }

        if (empty($eventKeys)) {
            throw new \Exception('Событие не найдено.', 404);
        }

        $eventKey = array_reduce(
            $eventKeys,
            static function ($c, $i) {
                if ($c === null) {
                    return $i;
                }
                $cParts = explode(':', $c);
                $iParts = explode(':', $i);
                return (int) end($cParts) > (int) end($iParts) ? $c : $i;
            }
        );

        $event = $this->memcached->get($eventKey);
        if ($event === false) {
            throw new \Exception('Событие не найдено.', 404);
        }

        $data = json_decode($event, true, flags: JSON_THROW_ON_ERROR);

        return GetEventModel::fromArray($data);
    }

    public function deleteAllEvents(): bool
    {
        $allKeys = $this->memcached->getAllKeys();
        $eventKeys = array_filter($allKeys, static function(string $key) {
            return str_starts_with($key, self::EVENTS_KEY_PREFIX) || str_starts_with($key, self::EVENT_CONDITIONS_KEY);
        });

        $result = $this->memcached->deleteMulti($eventKeys);

        if (in_array(false, $result, true)) {
            throw new \Exception('Не удалось удалить все события и связанные с ними данные. Попробуйте ещё раз.', 207);
        }

        return true;
    }
}
