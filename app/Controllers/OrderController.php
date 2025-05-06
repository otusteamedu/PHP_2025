<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Adapter\FastFoodItemInterface;
use App\Services\OrderService;
use ReflectionException;

class OrderController extends BaseController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly FastFoodItemInterface $fastFoodItem,
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function index(): array
    {
        $orderList = [];
        $orders = $this->orderService->getList();
        foreach ($orders as $order) {
            $orderList[] = $this->jsonResponse($order->toArray());
        }

        return $orderList;
    }

    /**
     * @return false|string
     */
    public function store(): false|string
    {
        $orderData = [
            'client_name' => $this->getRequestData()['client_name'],
            'client_phone' => $this->getRequestData()['client_phone'],
            'product_id' => $this->getRequestData()['product_id'],
            'ingredients' => $this->getRequestData()['ingredients'],
        ];

        $errors = $this->validate($orderData);

        if (!empty($errors)) {
            return $this->jsonResponse([
                'errors' => [
                    $errors
                ]
            ]);
        }

        $order = $this->orderService->createOrder($orderData);

        return $this->jsonResponse([
            'success' => 'Заказ успешно создан.',
            'order' => [
                $order->toArray()
            ]
        ]);
    }

    /**
     * @param int $id
     * @return false|string
     */
    public function show(int $id): false|string
    {
        $order = $this->orderService->getOrder($id);

        return $this->jsonResponse($order->toArray());
    }

    /**
     * @param int $id
     * @return false|string
     */
    public function orderPay(int $id): false|string
    {
        //условная оплата
        $payment = $this->orderService->payOrder($id, intval($this->getRequestData()['payment']));

        if (is_string($payment)) {
            return $this->jsonResponse(['message' => $payment]);
        }

        if (!$payment) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => 'Оплата не проведена.'
            ]);
        }

        return $this->jsonResponse([
            'status' => 'success',
            'message' => 'Оплата прошла успешно.'
        ]);
    }

    /**
     * @return mixed
     */
    public function makePizza(): mixed
    {
        return $this->fastFoodItem->prepare();
    }
}