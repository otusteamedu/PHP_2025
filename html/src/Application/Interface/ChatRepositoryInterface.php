<?php

declare(strict_types=1);

namespace Otus\Queue\Application\Interface;

use Otus\Queue\Domain\Entity\Message;

interface ChatRepositoryInterface
{
    /**
     * @param Message $message
     *
     * @return bool
     */
    public function store(Message $message): bool;

    /**
     * @param int $limit
     *
     * @return array
     */
    public function history(int $limit = 20): array;
}
