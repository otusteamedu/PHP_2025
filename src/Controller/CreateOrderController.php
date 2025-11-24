<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Controller;

use Dinargab\Homework15\Exception\DefectiveProductException;
use Dinargab\Homework15\Model\Order\ProductOrder;
use Dinargab\Homework15\Model\Order\ProductOrderFactory;
use Dinargab\Homework15\Service\Cooking\CookingProcess;
use Dinargab\Homework15\Service\Order\OrderHandler\CompletedOrderHandler;
use Dinargab\Homework15\Service\Order\OrderHandler\CookingOrderHandler;
use Dinargab\Homework15\Service\Order\OrderHandler\NewOrderHandler;
use Dinargab\Homework15\Service\Order\OrderHandler\ReadyOrderHandler;

class CreateOrderController extends AbstractController
{

    private ProductOrder $productOrder;

    public function __construct(
        private ProductOrderFactory $productOrderFactory,
        private CookingProcess $cookingProcess,
    )
    {
        $this->productOrder = $this->productOrderFactory->create();
    }
    public function __invoke(array $orderData) :void
    {
        try {
            $this->productOrder->setOrderedProducts($orderData);
            $orderHandler = new NewOrderHandler();
            $orderHandler
                ->setNext(new CookingOrderHandler($this->cookingProcess))
                ->setNext(new ReadyOrderHandler())
                ->setNext(new CompletedOrderHandler());

            $orderHandler->handle($this->productOrder);
            $this->render('index', [
                "orderId" => $this->productOrder->getId(),
                "products" => $this->productOrder->getProducts(),
                "totalPrice" => $this->productOrder->getTotalPrice(),
            ]);
        } catch (DefectiveProductException $exception) {
            $this->render('defective', ["message" => $exception->getMessage()]);
        }
    }

}