<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Domain\Mapper\BookMapper;
use App\Domain\Repository\BookRepositoryInterface;
use App\Domain\Repository\Pager;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Repository\BookRepository;
use App\Infrastructure\View\BookSearchView;

class SearchAction extends BaseAction
{
    private BookRepositoryInterface $bookRepository;
    private BookMapper $bookMapper;
    private BookSearchView $bookSearchView;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
        $this->bookMapper = new BookMapper();
        $this->bookSearchView = new BookSearchView();

    }

    public function __invoke(Request $request): Response
    {
        $data = $request->getParams();
        $filter = $this->bookMapper->buildFilter($data);
        $result = $this->bookRepository->search($filter);
        $pager = new Pager($filter->getPager()->page, $filter->getPager()->getLimit(), $result->total);
        $response = $this->bookSearchView->buildTable($result, $pager);

        return $this->responseSuccess($response);
    }
}