<?php declare(strict_types=1);

namespace App;

use App\Exception\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Mapper\OrderMapper;
use App\Mapper\UserMapper;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\OrderService;
use App\Service\UserService;

class App
{
    private Request $request;
    private Response $response;
    private UserService $userService;
    private OrderService $orderService;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->userService = $this->createUserService();
        $this->orderService = $this->createOrderService();
    }

    public function run(): Response
    {
        if ($this->request->isUrl('/orders')) {
            return $this->fetchOrders();
        }
        if ($this->request->isUrl('/create-user')) {
            return $this->createUser();
        }
        if ($this->request->isUrl('/create-order')) {
            return $this->createOrder();
        }

        return $this->response->send(200, []);
    }

    private function fetchOrders(): Response
    {
        try {
            $userId = $this->request->getQueryParam('user_id');

            if (isset($userId)) {
                $orders = $this->orderService->getOrdersWithUser((int)$userId);
            } else {
                $orders = $this->orderService->getOrders();
            }

            return $this->response->send(200, [
                'orders' => $orders
            ]);
        } catch (HttpException $e) {
            return $this->response->send(400, ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $this->response->send(500, ['error' => $e->getMessage()]);
        }
    }

    private function createUser(): Response
    {
        try {
            $data = $this->request->getJsonData();

            $createdUser = $this->userService->create(
                name: $data['name'],
                email: $data['email'],
            );

            return $this->response->send(200, [
                'user' => $createdUser
            ]);
        } catch (HttpException $e) {
            return $this->response->send(400, ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $this->response->send(500, ['error' => $e->getMessage()]);
        }
    }

    private function createOrder(): Response
    {
        try {
            $data = $this->request->getJsonData();

            $createdOrder = $this->orderService->create(
                totalAmount: $data['totalAmount'],
                userId: $data['userId']
            );

            return $this->response->send(200, [
                'order' => $createdOrder
            ]);
        } catch (HttpException $e) {
            return $this->response->send(400, ['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $this->response->send(500, ['error' => $e->getMessage()]);
        }
    }

    private function createUserService(): UserService
    {
        return new UserService(new UserRepository(new UserMapper()));
    }

    private function createOrderService(): OrderService
    {
        return new OrderService(new OrderRepository(new OrderMapper()));
    }
}
