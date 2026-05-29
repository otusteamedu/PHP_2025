<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\DTO;

use MkdBot\Application\DTO\NewsDeliveryDTO;
use PHPUnit\Framework\TestCase;

/**
 * Тесты DTO NewsDeliveryDTO
 */
class NewsDeliveryDTOTest extends TestCase
{
    public function testToArray(): void
    {
        $dto = new NewsDeliveryDTO(
            newsId: 1,
            userId: 12345,
            title: 'Заголовок',
            content: 'Текст новости',
        );

        $array = $dto->toArray();

        $this->assertEquals([
            'news_id' => 1,
            'user_id' => 12345,
            'title' => 'Заголовок',
            'content' => 'Текст новости',
        ], $array);
    }

    public function testFromArray(): void
    {
        $data = [
            'news_id' => 2,
            'user_id' => 67890,
            'title' => 'Тест',
            'content' => 'Содержание',
        ];

        $dto = NewsDeliveryDTO::fromArray($data);

        $this->assertEquals(2, $dto->newsId);
        $this->assertEquals(67890, $dto->userId);
        $this->assertEquals('Тест', $dto->title);
        $this->assertEquals('Содержание', $dto->content);
    }

    public function testFromArrayWithMissingFields(): void
    {
        $dto = NewsDeliveryDTO::fromArray([]);

        $this->assertEquals(0, $dto->newsId);
        $this->assertEquals(0, $dto->userId);
        $this->assertEquals('', $dto->title);
        $this->assertEquals('', $dto->content);
    }

    public function testRoundTrip(): void
    {
        $original = new NewsDeliveryDTO(
            newsId: 42,
            userId: 99999,
            title: 'Круглый путь',
            content: 'Туда и обратно',
        );

        $array = $original->toArray();
        $restored = NewsDeliveryDTO::fromArray($array);

        $this->assertEquals($original->newsId, $restored->newsId);
        $this->assertEquals($original->userId, $restored->userId);
        $this->assertEquals($original->title, $restored->title);
        $this->assertEquals($original->content, $restored->content);
    }
}
