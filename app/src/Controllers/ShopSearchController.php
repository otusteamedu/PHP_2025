<?php declare(strict_types=1);

namespace App\Controllers;

use App\Console\Exceptions\Exception;
use App\Forms\Search\BookSearch;
use App\Services\ShopSearchService;
use App\Services\ShopSearchServiceInterface;
use App\Views\BooksTableView;
use Throwable;

/**
 * Class ShopSearchController
 * @package App\Controllers
 */
class ShopSearchController
{
    /**
     * @var ShopSearchServiceInterface
     */
    private ShopSearchServiceInterface $service;
    /**
     * @var BooksTableView
     */
    private BooksTableView $view;

    /**
     *
     */
    public function __construct()
    {
        $this->service = new ShopSearchService();
        $this->view = new BooksTableView();
    }

    /**
     * @param array $params
     * @return void
     * @throws Exception
     */
    public function actionSearch(array $params): void
    {
        try {
            $bookSearch = new BookSearch($params);

            $totalNum = $this->service->count($bookSearch);
            $books = $this->service->search($bookSearch);

            echo 'Найдено книг: ' . $totalNum . PHP_EOL;
            $this->view->render($books);
        } catch (Throwable $e) {
            throw new Exception('Search error: ' . $e->getMessage());
        }
    }
}
