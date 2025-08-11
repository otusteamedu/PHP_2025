<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Subscriber;

interface SubscriberInterface
{
    public function update(OrderStatusChangedEvent $event): void;
}