<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Enum\ProposalType;

interface ProposalNotifierInterface
{
    /**
     * Отправить уведомление о новом предложении/функционале
     *
     * @param ProposalType $type Тип предложения (feature/suggestion)
     * @param string $subject Тема предложения
     * @param string $content Содержание предложения
     * @param string $authorName Имя автора
     * @param int $authorId ID автора в мессенджере
     */
    public function notify(ProposalType $type, string $subject, string $content, string $authorName, int $authorId): void;
}
