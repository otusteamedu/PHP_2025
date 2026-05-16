<?php

declare(strict_types=1);

namespace App\Delivery;

use App\DTO\StatementRequest;
use App\DTO\StatementResponse;
use App\Mail\MailerInterface;

/**
 * Стратегия доставки выписки по email.
 */
final class EmailStatementDeliveryStrategy implements StatementDeliveryStrategyInterface
{
    /**
     * @param MailerInterface $mailer Сервис отправки email.
     */
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * Проверяет, можно ли отправить выписку по email.
     *
     * @param StatementRequest $request Заявка на выписку.
     * @return bool
     */
    public function supports(StatementRequest $request): bool
    {
        return $request->hasEmail();
    }

    /**
     * Отправляет выписку на email из заявки.
     *
     * @param StatementRequest $request Заявка на выписку.
     * @param StatementResponse $response Сформированная выписка.
     */
    public function deliver(StatementRequest $request, StatementResponse $response): void
    {
        if ($request->email === null) {
            return;
        }

        $this->mailer->send($request->email, $response->subject, $response->body);

        echo 'Письмо с выпиской отправлено на ' . $request->email . PHP_EOL;
    }
}
