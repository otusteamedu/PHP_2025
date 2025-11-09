<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Publisher;

use Kamalo\BurgersShop\Domain\Subscriber\OrderStatusChangedEvent;
use Kamalo\BurgersShop\Domain\Subscriber\SubscriberInterface;

interface PublisherInterface
{
    public function subscribe(SubscriberInterface $subscriber): void;

    public function unsubscribe(SubscriberInterface $subscriber): void;

    public function notify(OrderStatusChangedEvent $event): void;
}