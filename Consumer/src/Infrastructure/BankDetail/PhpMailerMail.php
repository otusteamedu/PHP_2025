<?php

namespace Consumer\Infrastructure\BankDetail;

use Consumer\Application\BankDetail\BankDetailMailInterface;
use Consumer\Application\Mailer\PhpMailerService;
use Consumer\Domain\DTO\BankDetailDTO;
use PHPMailer\PHPMailer\Exception;

class PhpMailerMail implements BankDetailMailInterface
{
    private PhpMailerService $phpMailerService;

    public function __construct(PhpMailerService $phpMailerService) {
        $this->phpMailerService = $phpMailerService;
    }

    /**
     * @param BankDetailDTO $dto
     * @param string $subject
     * @param string $to
     * @param string|null $from
     * @return void
     * @throws Exception
     */
    public function mail(
        BankDetailDTO $dto,
        string $subject,
        string $to,
        ?string $from
    ): void {
        $body =
            "<b>Банк</b>: $dto->bank<br>" .
            "<b>Счет</b>: $dto->account<br>" .
            "<b>БИК</b>: $dto->bik<br>" .
            "<b>Клиент</b>: $dto->client<br>";

        $this->phpMailerService->mail($subject, $body, $to, $from);
    }
}