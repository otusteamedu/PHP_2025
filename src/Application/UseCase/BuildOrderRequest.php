<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\UseCase;

class BuildOrderRequest
{
    public function __construct(
        public readonly array $burgers,
        public readonly array $sandwiches,
        public readonly array $hotdogs
    ) {
    }
}