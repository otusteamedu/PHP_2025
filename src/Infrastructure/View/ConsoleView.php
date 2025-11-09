<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Infrastructure\View;

use Kamalo\BurgersShop\Domain\Subscriber\OrderStatusChangedEvent;
use Kamalo\BurgersShop\Domain\Subscriber\SubscriberInterface;

class ConsoleView implements SubscriberInterface
{
    public function update(OrderStatusChangedEvent $event): void
    {
        error_log("Статус приготовления: $event->statusCode");

    }
}