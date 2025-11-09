<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Infrastructure\Handler;

use Kamalo\BurgersShop\Application\Handler\CookHandler;
use Kamalo\BurgersShop\Application\Handler\CookHandlerRequest;
use Kamalo\BurgersShop\Domain\Builder\ProductBuilderInterface;
use Kamalo\BurgersShop\Domain\Entity\Order;


class SandwichCookHandler extends CookHandler
{
    public function __construct(
        private ProductBuilderInterface $builder
    ) {
    }

    public function cook(
        Order $order,
        CookHandlerRequest $buildRequest
    ): Order {

        if (count($buildRequest->sandwiches) > 0) {

            foreach ($buildRequest->sandwiches as $productCode => $sandwichParams) {

                $this->builder->setProductCode($productCode);

                foreach ($sandwichParams['extras'] as $extraIngredient => $count) {
                    
                    $this->builder->setExtraItem(
                        $extraIngredient,
                        $count
                    );
                }

                $sandwich = $this->builder->build();
               
                for ($i = 0; $i < $sandwichParams["count"]; $i++) {
                    $order->getProducts()->add($sandwich);
                }
            }
        }

        return parent::cook($order, $buildRequest);
    }
}