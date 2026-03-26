<?php

declare(strict_types=1);

namespace Api\Application\UseCases;

use Api\Domain\Entities\Request;
use Api\Domain\Enums\RequestStatus;
use Api\Domain\Interfaces\QueueInterface;
use Api\Domain\Interfaces\RepositoryInterface;
use InvalidArgumentException;

final class CreateRequestUseCase
{
    private const MIN_CONTENT_LENGTH = 1;
    private const MAX_CONTENT_LENGTH = 10000;

    public function __construct(
        private RepositoryInterface $repository,
        private QueueInterface $queue
    ) {
    }

    public function execute(string $content): int
    {
        $content = trim($content);
        $length = mb_strlen($content);

        if ($length < self::MIN_CONTENT_LENGTH) {
            throw new InvalidArgumentException(
                'Минимальная длина поля content: ' . self::MIN_CONTENT_LENGTH . ' символов'
            );
        }

        if ($length > self::MAX_CONTENT_LENGTH) {
            throw new InvalidArgumentException(
                'Максимальная длина поля content: ' . self::MAX_CONTENT_LENGTH . ' символов'
            );
        }

        $request = new Request(
            0,
            RequestStatus::PENDING,
            $content,
            new \DateTimeImmutable()
        );

        $savedRequest = $this->repository->add($request);

        $this->queue->enqueue(['id' => $savedRequest->id]);

        return $savedRequest->id;
    }
}
