<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\ProposalDTO;
use MkdBot\Domain\Entity\Proposal;
use MkdBot\Domain\Interface\ProposalNotifierInterface;
use MkdBot\Domain\Interface\ProposalRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

/**
 * Сохранение предложения в БД и отправка уведомления по email
 */
class ProcessProposal
{
    public function __construct(
        private readonly ProposalRepositoryInterface $proposalRepo,
        private readonly ProposalNotifierInterface $proposalNotifier,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Сохраняет предложение в БД и отправляет email-уведомление
     * Ошибка уведомления НЕ прерывает сохранение — пользователь уже видел «✅ Подтверждено»
     *
     * @throws RuntimeException при ошибке сохранения
     */
    public function execute(ProposalDTO $dto): void
    {
        try {
            $proposal = new Proposal(
                type: $dto->type,
                userId: $dto->userId,
                userName: $dto->userName,
                subject: $dto->subject,
                content: $dto->content,
            );
            $this->proposalRepo->save($proposal);
            $this->logger->info("Предложение сохранено: type={$dto->type->value}, userId={$dto->userId}");
        } catch (Throwable $e) {
            $this->logger->error("Ошибка сохранения предложения: " . $e->getMessage());
            throw new RuntimeException('Ошибка сохранения предложения', 0, $e);
        }

        // Уведомление: ошибка не должна ломать сохранение
        try {
            $this->proposalNotifier->notify(
                type: $dto->type,
                subject: $dto->subject,
                content: $dto->content,
                authorName: $dto->userName,
                authorId: $dto->userId,
            );
        } catch (Throwable $e) {
            $this->logger->error("Ошибка отправки уведомления о предложении: " . $e->getMessage());
        }
    }
}
