<?php

declare(strict_types=1);

namespace Otus\DataMapper\Command;

use Otus\DataMapper\Condition\Condition;
use Otus\DataMapper\Condition\Equal;
use Otus\DataMapper\Condition\In;
use Otus\DataMapper\Sql\Command;
use Otus\DataMapper\Sql\From;
use Otus\DataMapper\Sql\Limit;
use Otus\DataMapper\Sql\Select;
use Otus\DataMapper\Sql\Where;

readonly class SqlCommand
{
    /**
     * @return int
     */
    public function __invoke(): int
    {
        $command = new Command();

        $condition = new Condition(
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
                    new Equal('brand', 'DELL'),
                ),
            ),
            new In('id', [1, 2]),
        );

        $command
            ->select(new Select(['id']))
            ->from(new From('products'))
            ->where(new Where($condition))
            ->limit(new Limit(0, 1));

        print_r($command->toSql());
        print_r($command->toValues());

        return 0;
    }
}
