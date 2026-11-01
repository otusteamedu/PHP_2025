<?php

declare(strict_types=1);

namespace App\Infrastructure\Sms\SmsMock;

use App\Domain\OrderNotification\Contract\NotificationSmsSenderInterface;
use App\Domain\OrderNotification\Exception\Delivery\NotificationSendingException;
use App\Domain\OrderNotification\ValueObject\Message\SmsMessage;
use App\Domain\OrderNotification\ValueObject\Recipient\Phone;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationSmsSender implements NotificationSmsSenderInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $baseUrl,
    ) {
    }

    public function send(SmsMessage $message, Phone $phone): void
    {
        $url = $this->baseUrl . '/api/v1/send';

        $options = [
            'json' => [
                'to' => $phone->value(),
                'body' => $message->getText(),
            ],
        ];

        try {
            $response = $this->client->request('POST', $url, $options);
            $statusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            throw NotificationSendingException::sendFailedForSms($e);
        }

        if ($statusCode >= 400) {
            throw NotificationSendingException::sendFailedForSms(
                new \RuntimeException('SMS gateway returned status code: ' . $statusCode),
            );
        }
    }
}
