<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\Command\CommandHandlerInterface;
use App\Application\UseCase\Command\RemoveEvent\RemoveEventCommand;
use App\Application\UseCase\Command\RemoveEvent\RemoveEventCommandHandler;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class DeleteAction extends BaseAction
{
    private CommandHandlerInterface $commandHandler;

    public function __construct()
    {
        $this->commandHandler = new RemoveEventCommandHandler();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        $id = $request->get('id');
        if (is_null($id)) {
            throw new \InvalidArgumentException('Id parameter is required');
        }
        $command = new RemoveEventCommand($id);
        ($this->commandHandler)($command);

        return $this->responseSuccess(null)->asJson();
    }

}