<?php

namespace App\Infrastructure\Storage;

use App\Domain\Entity\Condition;
use App\Domain\Entity\Event;
use Exception;

interface StorageEventDBInterface
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
