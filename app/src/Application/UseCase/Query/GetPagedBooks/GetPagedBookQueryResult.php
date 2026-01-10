<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\GetPagedBooks;

use App\Domain\Repository\Pager;

class GetPagedBookQueryResult
{
    public function __construct(public array $books, public Pager $pager)
    {
    }

}