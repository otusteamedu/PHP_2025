<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Entity\Proposal;

/**
 * Интерфейс репозитория предложений
 */
interface ProposalRepositoryInterface
{
    /**
     * Сохраняет предложение в БД
     */
    public function save(Proposal $proposal): Proposal;
}
