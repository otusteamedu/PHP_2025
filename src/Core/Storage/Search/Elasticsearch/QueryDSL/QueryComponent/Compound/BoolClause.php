<?php

declare(strict_types=1);

namespace App\Core\Storage\Search\Elasticsearch\QueryDSL\QueryComponent\Compound;

enum BoolClause: string
{
    case Must = 'must';
    case Filter = 'filter';
    case Should = 'should';
    case MustNot = 'must_not';
}
