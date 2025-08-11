<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Subscriber;

class OrderStatusChangedEvent
{
    public function __construct(
        public readonly string $statusCode
    ) {
    }
}