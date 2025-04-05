<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Domain\Repository\BookRepositoryInterface;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Repository\BookRepository;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class DbDeleteAction extends BaseAction
{
    private BookRepositoryInterface $bookRepository;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();

    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        $dbName = $request->getParam('index');
        if (!$dbName) {
            throw new \Exception('db name is required');
        }
        $result = $this->bookRepository->dbDelete($dbName);
        if (!$result) {
            return $this->responseError('db deletion failed');
        }

        return $this->responseSuccess('db deleted successfully');

    }

}