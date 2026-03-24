<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Model\AddEventModel;
use App\Model\GetEventModel;
use App\Model\SearchEventModel;

class RedisEventRepository implements EventRepositoryInterface
{
    private readonly \Redis $redis;

    public function __construct(string $host)
    {
        $this->redis = new \Redis();
        if (!$this->redis->connect($host)) {
            throw new \RuntimeException('Не удалось установить связь с хранилищем.');
        }
    }

    public function addEvent(AddEventModel $addEventModel): bool
    {
        $zScoreResult = $this->redis->zScore(self::EVENT_CONDITIONS_KEY, $addEventModel->getPreparedConditions());
        if (is_float($zScoreResult)) {
            throw new \InvalidArgumentException('Событие с такими условиями уже существует.', 400);
        }

        $this->redis->multi();
        $this->redis->zAdd(
            self::EVENT_CONDITIONS_KEY,
            (float) $addEventModel->getPriority(),
            $addEventModel->getPreparedConditions(),
        );
        $this->redis->hMset(
            $addEventModel->getEventKey(),
            [
                'id' => $addEventModel->getId(),
                'name' => $addEventModel->getName(),
                'priority' => $addEventModel->getPriority(),
                'conditions' => $addEventModel->getPreparedConditions(),
            ],
        );
        $result = $this->redis->exec();

        if ($result === false) {
            throw new \Exception($this->redis->getLastError() ?? 'Внутренняя ошибка сервиса.', 500);
        }

        return true;
    }

    public function getEvent(SearchEventModel $searchEventModel): GetEventModel
    {
        $result = $this->redis->exists(self::EVENT_CONDITIONS_KEY);
        if ($result !== 1) {
            throw new \Exception('Событие не найдено.', 404);
        }

        $params = [];
        foreach ($searchEventModel->getPreparedParams() as $param) {
            $params[] = 0.0;
            $params[] = $param;
        }

        $result = $this->redis
            ->multi()
                ->zAdd(self::SEARCH_EVENT_PARAMS_KEY, ...$params)
                ->zinter(
                    [self::EVENT_CONDITIONS_KEY, self::SEARCH_EVENT_PARAMS_KEY],
                    options: ['WITHSCORES' => true],
                )
                ->del(self::SEARCH_EVENT_PARAMS_KEY)
            ->exec();

        if ($result === false) {
            throw new \Exception($this->redis->getLastError() ?? 'Внутренняя ошибка сервиса.', 500);
        }

        $results = [
            'zAdd' => $result[0],
            'zInter' => $result[1],
            'del' => $result[2],
        ];

        if (empty($results['zInter'])) {
            throw new \Exception('Событие не найдено.', 404);
        }

        $eventKeyPattern = $searchEventModel->getEventKeyPattern(max($results['zInter']));
        $eventKey = $this->redis->keys($eventKeyPattern)[0] ?? null;

        if ($eventKey === null) {
            throw new \Exception('Событие не найдено.', 404);
        }

        $result = $this->redis->hGetAll($eventKey);

        if (empty($result)) {
            throw new \Exception('Событие не найдено.', 404);
        }

        return new GetEventModel(
            id: $result['id'],
            name: $result['name'],
            priority: $result['priority'],
            conditions: $result['conditions'],
        );
    }

    public function deleteAllEvents(): bool
    {
        $eventKeys = $this->redis->keys(self::EVENTS_KEY_PREFIX . ':*');
        $eventKeys[] = self::EVENT_CONDITIONS_KEY;

        $result = $this->redis->del($eventKeys);

        if ($result === false) {
            throw new \Exception($this->redis->getLastError() ?? 'Внутренняя ошибка сервиса.', 500);
        }

        if ($result !== count($eventKeys)) {
            throw new \Exception('Не удалось удалить все события и связанные с ними данные. Попробуйте ещё раз.', 207);
        }

        return true;
    }
}
