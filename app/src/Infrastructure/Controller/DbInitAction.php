<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\UseCase\Command\InitDatabase\InitDataBaseCommand;
use App\Application\UseCase\Command\InitDatabase\InitDatabaseCommandHandler;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class DbInitAction extends BaseAction
{
    private InitDatabaseCommandHandler $handler;

    public function __construct()
    {
        $this->handler = new InitDatabaseCommandHandler();
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
        $command = new InitDatabaseCommand($dbName);
        ($this->handler)($command);

        return $this->responseSuccess('db created');
    }

}