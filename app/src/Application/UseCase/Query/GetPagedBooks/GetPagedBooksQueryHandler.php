<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\GetPagedBooks;

use App\Domain\Repository\BookRepositoryInterface;
use App\Domain\Repository\Pager;
use App\Infrastructure\Repository\BookRepository;

readonly class GetPagedBooksQueryHandler
{
    private BookRepositoryInterface $bookRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
    }

    public function __invoke(GetPagedBooksQuery $query): GetPagedBookQueryResult
    {
        $result = $this->bookRepository->search($query->filter);
        $pager = new Pager($query->filter->getPager()->page, $query->filter->getPager()->getLimit(), $result->total);

        return new GetPagedBookQueryResult($result->items, $pager);
    }

}