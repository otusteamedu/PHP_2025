<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\UseCase;

use Kamalo\BurgersShop\Domain\Manager\OrderManager;
use Kamalo\BurgersShop\Domain\Manager\OrderManagerRequest;

class BuildOrderUseCase
{
    public function __construct(private OrderManager $orderManager)
    {
    }

    public function __invoke(BuildOrderRequest $request): BuildOrderResponse
    {
        $order = $this->orderManager->manage(
            new OrderManagerRequest(
                $request->burgers,
                $request->sandwiches,
                $request->hotdogs
            )
        );

        return new BuildOrderResponse($order);
    }
}