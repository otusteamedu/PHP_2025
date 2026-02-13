<?php

declare(strict_types=1);

namespace App\Presentation;

use App\Application\Validator;
use App\Infrastructure\ApiService;
use App\Domain\OrderRepository;

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

    public function processOrder(array $orderData): void
    {
        $errors = $this->validator->validate($orderData);
        if (count($errors) > 0) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            return;
        }

        try {
            $this->apiService->charge($orderData);
            if ($this->orderRepository->setOrderIsPaid((string)$orderData['order_number'], (float)$orderData['sum'])) {
                http_response_code(200);
                echo json_encode(['status' => 'success']);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Order validation failed after payment.']);
            }
        } catch (\Exception $e) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
