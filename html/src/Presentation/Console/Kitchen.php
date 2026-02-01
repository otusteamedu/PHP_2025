<?php

declare(strict_types=1);

namespace Otus\Food\Presentation\Console;

use Otus\Food\Application\Strategy\Harvester;
use Otus\Food\Application\Strategy\HarvesterException;
use Otus\Food\Domain\Order\Buyer;
use Otus\Food\Domain\Order\Order;

final readonly class Kitchen
{
    /**
     * @param Harvester $harvester
     */
    public function __construct(
        private Harvester $harvester,
    ) {
    }

    /**
     * @param string $title
     * @param string $me
     * @param int $count
     *
     * @return int
     *
     * @throws HarvesterException
     */
    public function __invoke(string $title, string $me, int $count = 1): int
    {
        $strategy = $this->harvester->getStrategy($title);

        $meal = $strategy->cooking(new Order(new Buyer($me)));

        $this->render($meal->getRecept());

        while (--$count > 0) {
            $this->render($meal->clone()->getRecept());
        }

        return 0;
    }

    private function render(array $array): void
    {
        print_r($array);
    }
}
