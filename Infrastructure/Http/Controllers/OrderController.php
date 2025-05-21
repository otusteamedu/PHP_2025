<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use App\Core\Exceptions\NotFoundException;
use Infrastructure\Adapter\FastFoodItemInterface;
use Infrastructure\Http\Requests\OrderRequest;
use Infrastructure\Services\OrderService;
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
     * @param OrderRequest $request
     * @return false|string
     */
    public function store(OrderRequest $request): false|string
    {
        $orderData = $this->getRequestData();

        $errors = $request->validate($orderData);

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
     * @return string
     * @throws NotFoundException
     */
    public function show(int $id): string
    {
        $order = $this->orderService->getOrder($id);

        if ($order === false) {
            throw new NotFoundException('Order not found');
        }

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