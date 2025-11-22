<?php

namespace Blarkinov\Hw1500\Infrastructure\Http;

use Blarkinov\Hw1500\Application\Observer\OrderStatusObserver;
use Blarkinov\Hw1500\Application\UseCase\ChangeStatusUseCase;
use Blarkinov\Hw1500\Application\UseCase\CreateOrderUseCase;
use Blarkinov\Hw1500\Application\UseCase\Request\ChangeOrderStatusDto;
use Blarkinov\Hw1500\Application\UseCase\Request\NewOrderRequestDto;
use Blarkinov\Hw1500\Domain\Controller\ControllerInterface;
use Blarkinov\Hw1500\Infrastructure\Gateway\Sender\MailSender;
use Blarkinov\Hw1500\Infrastructure\Gateway\Sender\PushNotificationSender;
use Blarkinov\Hw1500\Infrastructure\Route\AsRoute;
use Exception;
use Framework\Http\Request;


final class OrderApiController implements ControllerInterface
{

    public function __construct(
        private Request $request,
        private CreateOrderUseCase $createOrder,
        private ChangeStatusUseCase $changeOrderStatus,
        private OrderStatusObserver $observer,
    ) {
        $observer->subscribe(new MailSender);
        $observer->subscribe(new PushNotificationSender);
    }

    #[AsRoute(path: '/api/orders/create')]
    public function createNewOrder(): array
    {

        try {
            $requestDto = new NewOrderRequestDto($this->request->getPostBag()['order']);

            $orderId = $this->createOrder->create($requestDto, $this->observer);
            return  ['id' => $orderId];
        } catch (\Throwable $e) {
            var_dump($e);
            throw new Exception($e->getMessage());
        }
    }

     #[AsRoute(path: '/api/orders/change')]
    public function changeStatus(): array
    {
        try {
            $postData = $this->request->getPostBag();
            $requestDto = new ChangeOrderStatusDto($postData['id'], $postData['status']);

            $this->changeOrderStatus->change($requestDto, $this->observer);

            return  ['id' => $requestDto->getId(), 'new status' => $requestDto->getStatus()];
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}
