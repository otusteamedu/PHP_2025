<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\GetPagedBooks;

use App\Domain\Repository\BookFilter;

class GetPagedBooksQuery
{
    public function __construct(public BookFilter $filter)
    {
    }

}