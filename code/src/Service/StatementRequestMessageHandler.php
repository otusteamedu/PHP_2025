<?php

declare(strict_types=1);

namespace App\Service;

use App\Delivery\StatementDeliveryFactory;
use App\DTO\StatementRequest;

/**
 * Обработчик сообщения с заявкой на банковскую выписку.
 */
final class StatementRequestMessageHandler
{
    private readonly StatementGeneratorService $statementGeneratorService;

    /**
     * @param StatementDeliveryFactory $deliveryFactory Фабрика стратегий доставки выписки.
     */
    public function __construct(
        private readonly StatementDeliveryFactory $deliveryFactory,
    ) {
        $this->statementGeneratorService = new StatementGeneratorService();
    }

    /**
     * Обрабатывает одно сообщение из очереди.
     *
     * @param string $message JSON-сообщение с данными заявки.
     */
    public function handle(string $message): void
    {
        $request = StatementRequest::fromJson($message);

        echo 'Получен запрос на выписку:' . PHP_EOL;
        echo 'ID заявки: ' . $request->requestId . PHP_EOL;
        echo 'Email: ' . ($request->email ?? 'не указан') . PHP_EOL;
        echo 'Счет: ' . $request->accountNumber . PHP_EOL;
        echo 'Период: ' . $request->dateFrom . ' - ' . $request->dateTo . PHP_EOL;

        $response = $this->statementGeneratorService->generate($request);

        foreach ($this->deliveryFactory->create($request) as $deliveryStrategy) {
            $deliveryStrategy->deliver($request, $response);
        }

        echo 'Запрос обработан.' . PHP_EOL;
    }
}
