<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\System;

use JsonException;
use Zibrov\OtusPhp2025\Redis\StorageInterface;

class EventSystem
{
    private const DEFAULT_KEY = 'event:';

    private StorageInterface $redisStorage;

    public function __construct(StorageInterface $redisStorage)
    {
        $this->redisStorage = $redisStorage;
    }

    /**
     * @throws JsonException
     */
    public function addEvent(int $priority, array $conditions, string $event): void
    {
        $uniqId = uniqid('event_', true);

        $status = $this->redisStorage->set(self::DEFAULT_KEY . $uniqId, json_encode([
            'priority' => $priority,
            'conditions' => $conditions,
            'event' => $event
        ], JSON_THROW_ON_ERROR));

        if ($status?->getPayload() === 'OK') {
            foreach ($conditions as $key => $value) {
                $this->redisStorage->sadd('conditions:' . $key . ':' . $value, [$uniqId]);
            }

            echo 'Добавлено событие: ' . $uniqId . PHP_EOL;
        } else {
            echo 'Событие ' . $uniqId . ' не добавлено' . PHP_EOL;
        }
    }

    /**
     * @throws JsonException
     */
    public function findSuitableEvent($arParams): void
    {
        if ($arEventKeys = $this->redisStorage->keys(self::DEFAULT_KEY . '*')) {
            foreach ($arEventKeys as $eventKey) {
                if ($json = $this->redisStorage->get($eventKey)) {
                    $arEvent = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

                    foreach ($arEvent['conditions'] as $condition => $value) {
                        if (isset($arParams[$condition]) && $arParams[$condition] === $value) {
                            if (empty($suitableEvent) || $arEvent['priority'] > $suitableEvent['priority']) {
                                $suitableEvent = $arEvent;
                            }
                            break;
                        }
                    }
                }
            }
        }

        if (!empty($suitableEvent)) {
            echo 'Лучшее событие: ' . json_encode($suitableEvent, JSON_THROW_ON_ERROR) . PHP_EOL;
        } else {
            echo 'Подходящее событие не найдено.' . PHP_EOL;
        }
    }

    public function clearAllEvents(): void
    {
        $keys = $this->redisStorage->keys(self::DEFAULT_KEY . '*');

        foreach ($keys as $key) {
            if ($this->redisStorage->del($key) === 1) {
                echo 'Событие ' . $key . ' удалено успешно!' . PHP_EOL;
            } else {
                echo 'Событие ' . $key . ' не удалено!' . PHP_EOL;
            }
        }
    }
}
