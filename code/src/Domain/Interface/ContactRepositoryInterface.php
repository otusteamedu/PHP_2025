<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Entity\Contact;
use MkdBot\Domain\Enum\ContactType;

/**
 * Интерфейс репозитория контактов (УК и совет дома)
 */
interface ContactRepositoryInterface
{
    /**
     * Возвращает все контакты заданного типа (uk | council)
     * @return Contact[]
     */
    public function findByType(ContactType $type): array;
}
