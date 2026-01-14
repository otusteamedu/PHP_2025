<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql\Command;

use Otus\DataMapper\Collection\Lazy;
use Otus\DataMapper\Condition\Condition;
use Otus\DataMapper\Condition\Equal;
use Otus\DataMapper\Condition\In;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Factory\RepositoryFactory;
use Otus\DataMapper\Repository\ProductRepository;
use Otus\DataMapper\Sql\Command;
use Otus\DataMapper\Sql\Limit;
use Otus\DataMapper\Sql\Where;

readonly class LazyCommand
{
    /**
     * @return int
     */
    public function __invoke(): int
    {
        /** @var ProductRepository $repository */
        $repository = RepositoryFactory::factory(Product::class);

        $command = new Command()
            ->where(
                new Where(
                    new Condition(
                        'OR',
                        new Condition(
                            'AND',
                            new Condition(
                                'AND',
                                new Equal('id', 1),
                                new Equal('brand', 'Apple'),
                            ),
                            new Condition(
                                'AND',
                                new Equal('id', 2),
                                new Equal('brand', 'XPS'),
                            ),
                        ),
                        new In('id', [1, 2]),
                    )
                )
            )
            ->limit(new Limit(0, 100));

        $result = $repository
            ->lazy($command);

        $this->render($result);

        return 0;
    }

    /**
     * @param Lazy $list
     */
    protected function render(Lazy $list): void
    {
        foreach ($list as $row) {
            print_r($row);
        }
    }
}
