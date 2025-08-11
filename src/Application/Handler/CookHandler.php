<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Handler;

use Kamalo\BurgersShop\Application\UseCase\BuildOrderResponse;
use Kamalo\BurgersShop\Domain\Entity\Order;

abstract class CookHandler
{
    private ?CookHandler $next = null;

    private BuildOrderResponse $response;

    public function setNext(CookHandler $next): CookHandler
    {
        $this->next = $next;
        return $next;
    }

    public function cook(
        Order $order,
        CookHandlerRequest $buildRequest
    ): Order {
    
        if ($this->next !== null) {
            $order = $this->next->cook($order, $buildRequest);
        }

        return $order;
    }
}