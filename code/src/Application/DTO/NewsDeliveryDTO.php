<?php

declare(strict_types=1);

namespace MkdBot\Application\DTO;

/**
 * DTO доставки новости конкретному пользователю
 * Передаётся через RabbitMQ очередь mkd.news.delivery
 */
class NewsDeliveryDTO
{
    public function __construct(
        public readonly int $newsId,
        public readonly int $userId,
        public readonly string $title,
        public readonly string $content,
    ) {
    }

    /**
     * Сериализация в массив для публикации в очередь
     */
    public function toArray(): array
    {
        return [
            'news_id' => $this->newsId,
            'user_id' => $this->userId,
            'title' => $this->title,
            'content' => $this->content,
        ];
    }

    /**
     * Десериализация из массива (из очереди)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            newsId: (int)($data['news_id'] ?? 0),
            userId: (int)($data['user_id'] ?? 0),
            title: (string)($data['title'] ?? ''),
            content: (string)($data['content'] ?? ''),
        );
    }
}
