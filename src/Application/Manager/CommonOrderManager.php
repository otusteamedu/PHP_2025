<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Manager;

use Kamalo\BurgersShop\Application\Client\BuildOrderClient;
use Kamalo\BurgersShop\Application\Gateway\PaymentGatewayInterface;
use Kamalo\BurgersShop\Application\Handler\CookHandlerRequest;
use Kamalo\BurgersShop\Application\Publisher\Publisher;
use Kamalo\BurgersShop\Domain\Entity\Order;
use Kamalo\BurgersShop\Domain\Manager\OrderManager;
use Kamalo\BurgersShop\Domain\Manager\OrderManagerRequest;
use Kamalo\BurgersShop\Domain\Subscriber\OrderStatusChangedEvent;

class CommonOrderManager extends OrderManager
{
    public function __construct(
        protected BuildOrderClient $buildOrderClient,
        protected PaymentGatewayInterface $paymentGateway,
        protected Publisher $publisher
    ) {
    }

    protected function create(OrderManagerRequest $request)
    {
        $this->publisher->notify(new OrderStatusChangedEvent('created'));

        $order = $this->buildOrderClient->cook(
            new Order(),
            new CookHandlerRequest(
                $request->burgers,
                $request->hotdogs,
                $request->sandwiches
            )
        );

       
        $order->changeStatus("created");

        return $order;
    }

    protected function check(Order $order): bool
    {
        if ($this->paymentGateway->pay($order)) {
            return true;
        }
        return false;

    }

    protected function cancel(Order $order)
    {
        $this->publisher->notify(new OrderStatusChangedEvent('canceled'));

        return $order->changeStatus("canceled");
    }

    protected function ready(Order $order)
    {
        $this->publisher->notify(new OrderStatusChangedEvent('ready'));

        return $order->changeStatus("ready");
    }
}