<?php

declare(strict_types=1);

namespace Ak\Hw\Presentation;

use Ak\Hw\Application\Validator;
use Ak\Hw\Domain\OrderRepository;
use Ak\Hw\Infrastructure\ApiService;

class OrderController
{
    private Validator $validator;
    private ApiService $apiService;
    private OrderRepository $orderRepository;

    public function __construct(Validator $validator, ApiService $apiService, OrderRepository $orderRepository)
    {
        $this->validator = $validator;
        $this->apiService = $apiService;
        $this->orderRepository = $orderRepository;
    }

    public function showOrderForm(array $errors = [], array $data = [], ?string $successMessage = null): void
    {
        OrderFormView::render($errors, $data, $successMessage);
    }

    /**
     * @throws \JsonException
     */
    public function processOrder(array $orderData): void
    {

        if (isset($orderData['sum'])) {
            $orderData['sum'] = (string) str_replace(',', '.', $orderData['sum']);
        }

        $errors = $this->validator->validate($orderData);
        if (count($errors) > 0) {
            http_response_code(400);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['errors' => $errors], JSON_THROW_ON_ERROR);
            } else {
                $this->showOrderForm($errors, $orderData);
            }
            return;
        }

        try {
            $this->apiService->charge($orderData);
            if ($this->orderRepository->setOrderIsPaid((string)$orderData['order_number'], (float)$orderData['sum'])) {
                // Если это AJAX-запрос, вернем JSON, иначе покажем форму с успехом
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    http_response_code(200);
                    echo json_encode(['status' => 'success'], JSON_THROW_ON_ERROR);
                } else {
                    $this->showOrderForm([], [], 'Payment was successful!');
                }
            } else {
                throw new \Exception('Order validation failed after payment.');
            }
        } catch (\Exception $e) {
            http_response_code(403);
            $error_message = $e->getMessage();
            // Если это AJAX-запрос, вернем JSON, иначе покажем форму с ошибкой
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['status' => 'error', 'message' => $error_message], JSON_THROW_ON_ERROR);
            } else {
                $this->showOrderForm(['general' => $error_message], $orderData);
            }
        }
    }
}
