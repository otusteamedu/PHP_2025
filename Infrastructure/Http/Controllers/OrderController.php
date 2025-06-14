<?php

declare(strict_types=1);

namespace Infrastructure\Http\Controllers;

use Infrastructure\Adapter\FastFoodItemInterface;
use Infrastructure\Http\Requests\OrderRequest;
use Infrastructure\Rabbit\Publisher;
use Infrastructure\Services\OrderService;

/**
 * @OA\Schema(
 *     schema="OrderResponse",
 *     @OA\Property(property="success", type="string"),
 *     @OA\Property(
 *         property="order",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Order")
 *     )
 * )
 */

class OrderController extends BaseController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly FastFoodItemInterface $fastFoodItem,
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/orders/list",
     *     tags={"Orders"},
     *     summary="Get list of all orders",
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Order")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/orders/new_order",
     *     tags={"Orders"},
     *     summary="Create new order",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(ref="#/components/schemas/NewOrderRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
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

        if ($order) {
            $publisher = new Publisher();
            $publisher->publish($order);
        } else {

            return $this->jsonResponse([
                'error' => 'Произошла ошибка. Попробуйте ещё раз.'
            ]);
        }

        return $this->jsonResponse([
            'success' => 'Заказ успешно создан.',
            'order' => [
                $order->toArray()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get order by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function show(int $id): false|string
    {
        $order = $this->orderService->getOrder($id);

        return $this->jsonResponse($order->toArray());
    }

    /**
     * @OA\Post(
     *     path="/orders/{id}/pay",
     *     tags={"Orders"},
     *     summary="Pay for order",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="payment",
     *                     type="number"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid payment amount"
     *     )
     * )
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
     * @OA\Get(
     *     path="/orders/pizza",
     *     tags={"Orders"},
     *     summary="Make pizza",
     *     @OA\Response(
     *         response=200,
     *         description="Pizza prepared",
     *         @OA\MediaType(
     *             mediaType="text/plain",
     *             @OA\Schema(type="string")
     *         )
     *     )
     * )
     */
    public function makePizza(): mixed
    {
        return $this->fastFoodItem->prepare();
    }
}

