<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\UseCase;

use Kamalo\BurgersShop\Domain\Entity\Order;

class BuildOrderResponse{

    public function __construct(
        public readonly ?Order $order
    ){}
}