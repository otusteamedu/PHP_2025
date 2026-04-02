<?

declare(strict_types=1);

namespace Kamalo\EventsService\Infrastucture\Repository;

use Kamalo\EventsService\Domain\Entity\Event;
use Kamalo\EventsService\Domain\Repository\EventRepositoryInterface;
use Kamalo\EventsService\Domain\Factory\EventFactoryInterface;
use Kamalo\EventsService\Application\Factory\EventFactory;
use Kamalo\EventsService\Infrastucture\Enviropment\Config;

class MemcachedEventRepository implements EventRepositoryInterface
{
    private \Memcached $memcached;
    private EventFactoryInterface $factory;
    public function __construct()
    {
        try {
            $this->memcached = new \Memcached();
            $this->memcached->addServer('memcached', 11211);
        } catch (\Throwable $e) {
            throw new \Exception('Ошибка при подключении к Memcached: ' . $e->getMessage());
        }

        $this->factory = new EventFactory();

        if (
            !$this->memcached->get('last:id')
        ) {
            $this->memcached->set('last:id', 0);
        }
    }

    public function findAll(): array
    {

        $keys = $this->getKeys();

        $events = [];

        if (count($keys) === 0) {
            return [];
        }

        foreach ($keys as $key) {
            $data = $this->memcached->get($key);

            if ($data !== false) {
                $events[] = $this->factory->createFromArray($data);
            }
        }

        return $events;
    }

    public function findByParams(array $params): ?Event
    {
        $keys = $this->getKeys();
        $events = [];
        $filteredKeys = [];

        if (count($keys) === 0) {
            return null;
        }

        $maxPriority = 0;

        foreach ($keys as $key) {
            $data = $this->memcached->get($key);

            $filteredKeys[$key] = 0;

            if ($data !== false) {

                foreach ($params as $field => $value) {

                    if (isset($data['conditions:' . $field]) && $data['conditions:' . $field] == $value) {
                        $filteredKeys[$key]++;
                    }
                }

                if (count($data) - $filteredKeys[$key] == 2) {
                
                    if ($data['priority'] >= $maxPriority) {
                        $events[] = $this->factory->createFromArray($data);
                        $maxPriority = $data['priority'];
                    }
                }
            }
        }

        if (count($events) === 0) {
            return null;
        }

        return $this->getMaxPriorityEvent(...$events);
    }

    public function add(Event $event): void
    {
        $event->setId($this->memcached->increment('last:id') ?: 1);

        if (
            !$this->memcached->set(
                'event:' . $event->getId(),
                $event->jsonSerialize()
            )
        ) {
            throw new \Exception('Ошибка при добавлении события с ' . $event->getId());
        }
    }

    public function clear(): void
    {
        try {
            $this->memcached->flush();
        } catch (\Throwable $e) {
            throw new \Exception("Ошибка при удалении всех событий");
        }

        $this->memcached->set('last:id', 0);
    }

    private function getKeys(): array
    {
        $lastId = $this->memcached->get('last:id') ?: 0; 
        $keys = [];

        for ($i = 1; $i <= $lastId; $i++) {
            $keys[] = 'event:' . $i; 
        }

        return $keys;
    }
    private function getMaxPriorityEvent(Event ...$events): Event
    {
        $maxPriorityEvent = $events[0];

        foreach ($events as $event) {
            if ($event->getPriority() > $maxPriorityEvent->getPriority()) {
                $maxPriorityEvent = $event;
            }
        }
        return $maxPriorityEvent;
    }
}