<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\Command\CommandHandlerInterface;
use App\Application\UseCase\Command\AddEvent\AddEventCommand;
use App\Application\UseCase\Command\AddEvent\AddEventCommandHandler;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class AddAction extends BaseAction
{
    private CommandHandlerInterface $commandHandler;

    public function __construct()
    {
        $this->commandHandler = new AddEventCommandHandler();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): Response
    {
        extract($request->postAll());

        if (!isset($priority) || !is_int($priority)) {
            throw new \InvalidArgumentException('Priority parameter is required and must be integer');
        }
        if (!isset($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Name parameter is required and must be string');
        }
        if (!isset($conditions) || !array($conditions)) {
            throw new \InvalidArgumentException('Conditions parameter is required and must be array');
        }

        $command = new AddEventCommand($priority, $name, $conditions);
        $result = ($this->commandHandler)($command);

        return $this->responseSuccess($result)->asJson();
    }

}