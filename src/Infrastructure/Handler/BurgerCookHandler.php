<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Infrastructure\Handler;

use Kamalo\BurgersShop\Application\Handler\CookHandler;
use Kamalo\BurgersShop\Application\Handler\CookHandlerRequest;
use Kamalo\BurgersShop\Domain\Builder\ProductBuilderInterface;
use Kamalo\BurgersShop\Domain\Entity\Order;

class BurgerCookHandler extends CookHandler
{
    public function __construct(
        private ProductBuilderInterface $builder
    ) {
    }

    public function cook(
        Order $order,
        CookHandlerRequest $buildRequest
    ): Order {

        if (count($buildRequest->burgers) > 0) {

            foreach ($buildRequest->burgers as $productCode => $burgerParams) {

                $this->builder->setProductCode($productCode);

                foreach ($burgerParams['extras'] as $extraIngredient => $count) {
                
                    $this->builder->setExtraItem(
                        $extraIngredient,
                        $count
                    );
                }

                $burger = $this->builder->build();

                for ($i = 0; $i < $burgerParams['count']; $i++) {
                    $order->getProducts()->add($burger);
                }
            }
        }

        return parent::cook($order, $buildRequest);
    }
}