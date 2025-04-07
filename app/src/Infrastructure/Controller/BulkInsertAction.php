<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\UseCase\Command\AddBooks\AddBooksCommand;
use App\Application\UseCase\Command\AddBooks\AddBooksCommandHandler;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class BulkInsertAction extends BaseAction
{
    private AddBooksCommandHandler $commandHandler;

    public function __construct()
    {
        $this->commandHandler = new AddBooksCommandHandler();
    }

    /**
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
        $command = new AddBooksCommand($dbName, $filePath);
        ($this->commandHandler)($command);

        return $this->responseSuccess('insert succeed');
    }


}