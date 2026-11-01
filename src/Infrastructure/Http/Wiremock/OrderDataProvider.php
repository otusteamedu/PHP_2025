<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Wiremock;

use App\Domain\OrderNotification\Contract\OrderDataProviderInterface;
use App\Domain\OrderNotification\Exception\OrderSource\OrderDataInvalidException;
use App\Domain\OrderNotification\Exception\OrderSource\OrderDataProvidingException;
use App\Domain\OrderNotification\ValueObject\Order\Money;
use App\Domain\OrderNotification\ValueObject\Order\OrderData;
use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;
use App\Domain\OrderNotification\ValueObject\Order\OrderId;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrderDataProvider implements OrderDataProviderInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $baseUrl,
    ) {
    }

    public function getOrderData(OrderId $id, OrderEvent $event): OrderData
    {
        $orderId = $id->value();
        $endpoint = $this->baseUrl . '/orders/' . $orderId;

        $options = [
            'query' => [
                'event' => $event->value,
            ],
        ];

        try {
            $data = $this->client->request('GET', $endpoint, $options)->toArray();
        } catch (ClientExceptionInterface $e) {
            throw OrderDataInvalidException::httpError(
                "Сервис заказов отклонил запрос для заказа № $orderId: " . $e->getMessage(),
            );
        } catch (DecodingExceptionInterface $e) {
            throw OrderDataInvalidException::invalidPayload(
                "Не удалось разобрать ответ для заказа № $orderId: " . $e->getMessage(),
            );
        } catch (TransportExceptionInterface | ServerExceptionInterface | RedirectionExceptionInterface $e) {
            throw OrderDataProvidingException::unreachable($e);
        }

        return $this->mapPayload($data, $orderId);
    }

    private function mapPayload(array $data, int $orderId): OrderData
    {
        $missing = [];

        foreach ([
            'customerName' => $data['customerName'] ?? null,
            'total.amount' => $data['total']['amount'] ?? null,
            'total.currency' => $data['total']['currency'] ?? null,
            'itemsCount' => $data['itemsCount'] ?? null,
        ] as $field => $value) {
            if ($value === null) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw OrderDataInvalidException::invalidPayload(
                "В ответе для заказа № $orderId отсутствуют поля: " . implode(', ', $missing),
            );
        }

        return new OrderData(
            customerName: $data['customerName'],
            total: new Money(
                amount: round((float) $data['total']['amount'], 2),
                currency: $data['total']['currency'],
            ),
            itemsCount: (int) $data['itemsCount'],
            trackingNumber: $data['trackingNumber'] ?? null,
        );
    }
}
