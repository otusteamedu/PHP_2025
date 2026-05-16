<?php

declare(strict_types=1);

namespace App\Delivery;

use App\Mail\MailerInterface;

/**
 * Провайдер фабрики доставки с набором стандартных стратегий.
 */
final class StatementDeliveryFactoryProvider
{
    /**
     * @param MailerInterface $mailer Сервис отправки email.
     */
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * Создает фабрику доставки со стандартными стратегиями.
     *
     * @return StatementDeliveryFactory
     */
    public function create(): StatementDeliveryFactory
    {
        return new StatementDeliveryFactory([
            new EmailStatementDeliveryStrategy($this->mailer),
            new BranchStatementDeliveryStrategy(),
        ]);
    }
}
