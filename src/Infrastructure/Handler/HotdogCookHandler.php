<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Infrastructure\Handler;

use Kamalo\BurgersShop\Application\Client\BuildOrderClientRequest;
use Kamalo\BurgersShop\Application\Handler\CookHandler;
use Kamalo\BurgersShop\Application\Handler\CookHandlerRequest;
use Kamalo\BurgersShop\Application\Handler\CookHandlerResponse;
use Kamalo\BurgersShop\Application\UseCase\BuildOrderRequest;
use Kamalo\BurgersShop\Domain\Builder\ProductBuilderInterface;
use Kamalo\BurgersShop\Domain\Entity\Order;


class HotdogCookHandler extends CookHandler
{
    public function __construct(
        private ProductBuilderInterface $builder
    ) {
    }

    public function cook(
        Order $order,
        CookHandlerRequest $buildRequest
    ): Order {

        if (count($buildRequest->hotdogs) > 0) {

            foreach ($buildRequest->hotdogs as $productCode => $hotdogParams) {
                
                $this->builder->setProductCode($productCode);
               
                foreach ($hotdogParams['extras'] as $extraIngredient => $count) {

                    $this->builder->setExtraItem(
                        $extraIngredient,
                        $count
                    );
                }

                $hotdog = $this->builder->build();
                
                for ($i = 0; $i < $hotdogParams["count"]; $i++) {
                    $order->getProducts()->add($hotdog);
                }

            }
        }

        return parent::cook($order, $buildRequest);
    }
}