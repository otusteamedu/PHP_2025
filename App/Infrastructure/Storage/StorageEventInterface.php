<?php

namespace App\Infrastructure\Storage;

use App\Model\Condition;
use App\Model\Event;
use Exception;

interface StorageEventInterface
{
    /** Сохраняем событие
     * @param Event $event
     * @return void
     */
    public function saveEvent(Event $event): void;

    /** Ищем событие
     * @param Condition[] $searchConditions
     * @return Event
     * @throws Exception
     */
    public function searchEvent(array $searchConditions): Event;

    /** Очищаем БД
     * @return void
     */
    public function clearEvents(): void;

    /** Наполняем тестовыми данными
     * @param Event[] $dataEvents
     * @return void
     */
    public function dataToDatabase(array $dataEvents): void;
}
