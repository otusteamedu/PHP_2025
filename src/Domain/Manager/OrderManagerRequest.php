<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Manager;

class OrderManagerRequest
{
    public function __construct(
        public readonly array $burgers,
        public readonly array $sandwiches,
        public readonly array $hotdogs
    ) {
    }
}