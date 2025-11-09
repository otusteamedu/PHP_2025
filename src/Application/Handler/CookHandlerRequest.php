<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Handler;

class CookHandlerRequest
{
    public function __construct(
        public array $burgers = [],
        public array $hotdogs = [],
        public array $sandwiches = []
    ) {
    }
}