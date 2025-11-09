<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Manager;

use Kamalo\BurgersShop\Domain\Entity\Order;
use Throwable;

abstract class OrderManager
{
    public function manage(OrderManagerRequest $buildRequest): ?Order
    {
        $order = $this->create($buildRequest);

        try {
            $this->check($order);
        } catch (Throwable $e) {
            $this->cancel($order);
            return null;
        }

        $this->ready($order);
        
        return $order;
    }

    abstract protected function create(OrderManagerRequest $buildRequest);

    abstract protected function check(Order $order): bool;

    abstract protected function cancel(Order $order);

    abstract protected function ready(Order $order);
}