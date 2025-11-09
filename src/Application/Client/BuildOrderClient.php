<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Client;

use Exception;
use Kamalo\BurgersShop\Application\Handler\CookHandler;
use Kamalo\BurgersShop\Application\Handler\CookHandlerRequest;
use Kamalo\BurgersShop\Application\UseCase\BuildOrderResponse;
use Kamalo\BurgersShop\Domain\Entity\Order;

class BuildOrderClient
{
    private BuildOrderResponse $response;
    public function __construct(
        private CookHandler $firstHandler
    ) {
    }

    public function cook(
        Order $order,
        CookHandlerRequest $request
    ): Order {
        try {
            return $this->firstHandler->cook($order, $request);
        } catch (\Throwable $e) {
            throw new Exception('Невозможно приготовить: ' . $e->getMessage());
        }
    }
}