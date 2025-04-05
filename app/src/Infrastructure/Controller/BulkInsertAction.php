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

class BulkInsertAction extends BaseAction
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
        $filePath = $request->getParam('filepath');
        if (!$dbName) {
            throw new \Exception('db name is required');
        }
        if (!$filePath) {
            throw new \Exception('filepath is required');
        }
        $itemsData = $this->getItems($filePath);

        $result = $this->bookRepository->bulkInsert($itemsData, $dbName);

        if ($result['errors']) {
            return $this->responseError('insert failed');
        }

        return $this->responseSuccess('insert succeed');
    }

    private function getItems(string $pathToFile): string
    {
        if (!file_exists($pathToFile)) {
            throw new \Exception('file not found');
        }

        return file_get_contents($pathToFile);
    }

}