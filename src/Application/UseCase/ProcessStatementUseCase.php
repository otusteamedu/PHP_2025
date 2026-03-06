<?php
declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Service\BankStatementService;
use App\Infrastructure\Mailer\Mailer;
use JsonException;
use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class ProcessStatementUseCase
{
    public function __construct(
        private readonly BankStatementService $bankService,
        private readonly Mailer $mailer,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @param string $messageBody
     * @return void
     * @throws Exception
     * @throws JsonException
     */
    public function handle(string $messageBody): void
    {
        $data = json_decode($messageBody, true, 512, JSON_THROW_ON_ERROR);

        $this->assertValidMessage($data);

        $id = (string) $data['request_id'];
        $email = (string) $data['email'];
        $dateFrom = (string) $data['date_from'];
        $dateTo = (string) $data['date_to'];

        $statement = $this->bankService->generateStatement($dateFrom, $dateTo);
        $this->mailer->sendStatement($email, $statement);

        $this->logger->info('Statement processed', [
            'request_id' => $id,
            'email' => $email,
            'from' => $dateFrom,
            'to' => $dateTo,
        ]);
    }

    private function assertValidMessage(array $data): void
    {
        $id = (string) ($data['request_id'] ?? '');
        $email = (string) ($data['email'] ?? '');
        $dateFrom = (string) ($data['date_from'] ?? '');
        $dateTo = (string) ($data['date_to'] ?? '');

        if ($id === '' || $email === '' || $dateFrom === '' || $dateTo === '') {
            throw new \InvalidArgumentException('Missing required fields');
        }

        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException('Invalid request_id');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }

        $from = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateFrom);
        $to = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateTo);

        if (!$from || $from->format('Y-m-d') !== $dateFrom) {
            throw new \InvalidArgumentException('Invalid date_from');
        }

        if (!$to || $to->format('Y-m-d') !== $dateTo) {
            throw new \InvalidArgumentException('Invalid date_to');
        }

        if ($from > $to) {
            throw new \InvalidArgumentException('date from cannot be later than date_to');
        }
    }
}
