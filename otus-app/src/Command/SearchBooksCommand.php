<?php

declare(strict_types=1);

namespace App\Command;

use App\Dto\SearchBookParamListDto;
use App\Service\BookService;
use App\Service\TableService;
use JsonException;

class SearchBooksCommand
{
    public function __construct(
        private BookService $bookService,
        private TableService $tableService,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function run(array $argv): string
    {
        $action = 'defaultSearch';
        $searchParamListDto = new SearchBookParamListDto(
            query: !empty($argv[2]) ? $argv[2] : null,
            maxPrice: !empty($argv[3]) ? (int) $argv[3] : null,
            minPrice: !empty($argv[4]) ? (int) $argv[4] : null,
            category: !empty($argv[5]) ? $argv[5] : null,
        );

        if (
            $searchParamListDto->category === null
            && $searchParamListDto->query === null
            && $searchParamListDto->maxPrice === null
            && $searchParamListDto->minPrice === null
        ) {
            $action = 'getAll';
        }

        $results = $this->bookService->getBookList($action, $searchParamListDto);
        $this->tableService->showBooksAsTable($results);

        return 'Success';
    }
}
