<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\UseCase\Query\GetPagedBooks\GetPagedBooksQuery;
use App\Application\UseCase\Query\GetPagedBooks\GetPagedBooksQueryHandler;
use App\Domain\Mapper\BookMapper;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\View\BookSearchView;

class SearchAction extends BaseAction
{
    private BookMapper $bookMapper;
    private BookSearchView $bookSearchView;
    private GetPagedBooksQueryHandler $getPagedBooksQueryHandler;

    public function __construct()
    {
        $this->bookMapper = new BookMapper();
        $this->bookSearchView = new BookSearchView();
        $this->getPagedBooksQueryHandler = new GetPagedBooksQueryHandler();
    }

    public function __invoke(Request $request): Response
    {
        $data = $request->getParams();
        $filter = $this->bookMapper->buildFilter($data);
        $query = new GetPagedBooksQuery($filter);
        $pagedResult = ($this->getPagedBooksQueryHandler)($query);
        $view = $this->bookSearchView->buildTable($pagedResult->books, $pagedResult->pager);

        return $this->responseSuccess($view);
    }
}