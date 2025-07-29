<?

declare(strict_types=1);

namespace Kamalo\EventsService\Domain\Factory;

use Kamalo\EventsService\Domain\Entity\Event;

interface EventFactoryInterface
{
    public static function createFromArray(array $data): Event;
    public static function create(int $id, int $priority, array $conditions): Event;

}
