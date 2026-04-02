<?

declare(strict_types=1);

namespace Kamalo\EventsService\Application\Factory;

use Kamalo\EventsService\Domain\Entity\Event;
use Kamalo\EventsService\Domain\Factory\EventFactoryInterface;

class EventFactory implements EventFactoryInterface
{
    public static function createFromArray(array $data): Event
    {
        $id = $data['id'];

        if ($id === null) {
            throw new \Exception('Id не может быть null');
        }
        $priority = $data['priority'];

        $pattern = '/conditions:/';

        $conditions = array_filter($data, function ($value, $key) use ($pattern) {
            return preg_match($pattern, $key);
        }, ARRAY_FILTER_USE_BOTH);

        return new Event(
            intval($id),
            intval($priority),
            $conditions
        );
    }

    public static function create(?int $id, int $priority, array $conditions): Event
    {
        return new Event(
            null,
            $priority,
            $conditions
        );
    }
}

