<?php
declare(strict_types=1);

namespace App\Classes;

use PHPMailer\PHPMailer\Exception;

class JobProcessor
{

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function handle(array $arData): void
    {
        $requestId = $arData['requestId'] ?: null;
        if (!$requestId) {
            throw new \InvalidArgumentException('В пакете отсутствует requestId');
        }

        sleep(10);

        error_log('Processed: ' . json_encode($arData, JSON_THROW_ON_ERROR));

        (new MailService())->send($arData['email']);
        (new TelegramService())->notify($arData['phone']);

        $statusStore = new StatusStore();
        $statusStore->set($requestId, 'done');
    }
}
