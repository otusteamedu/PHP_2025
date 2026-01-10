<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\FindOneByCondition;

use App\Application\Query\QueryInterface;

class FindOneByConditionQuery implements QueryInterface
{
    public function __construct(public array $conditions)
    {
    }

}