<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\UseCase\Command\DeleteDatabase\DeleteDataBaseCommand;
use App\Application\UseCase\Command\DeleteDatabase\DeleteDatabaseCommandHandler;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class DbDeleteAction extends BaseAction
{
    private DeleteDatabaseCommandHandler $handler;

    public function __construct()
    {
        $this->handler = new DeleteDatabaseCommandHandler();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        $dbName = $request->getParam('index');
        if (!$dbName) {
            throw new \Exception('db name is required');
        }
        $command = new DeleteDatabaseCommand($dbName);
        ($this->handler)($command);

        return $this->responseSuccess('db deleted successfully');

    }

}