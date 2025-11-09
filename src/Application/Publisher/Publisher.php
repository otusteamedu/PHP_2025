<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Publisher;

use Kamalo\BurgersShop\Domain\Publisher\PublisherInterface;
use Kamalo\BurgersShop\Domain\Subscriber\OrderStatusChangedEvent;
use Kamalo\BurgersShop\Domain\Subscriber\SubscriberInterface;

class Publisher implements PublisherInterface
{
    private array $subscribers;

    public function subscribe(SubscriberInterface $subscriber): void
    {
        $this->subscribers[] = $subscriber;
    }

    public function unsubscribe(SubscriberInterface $subscriber): void
    {
        $key = array_search($subscriber, $this->subscribers);
        unset($this->subscribers[$key]);

    }

    public function notify(OrderStatusChangedEvent $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->update($event);
        }
    }
}