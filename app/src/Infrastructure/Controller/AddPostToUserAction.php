<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\Command\CommandHandlerInterface;
use App\Application\UseCase\Command\AddPostToUser\AddPostToUserCommand;
use App\Application\UseCase\Command\AddPostToUser\AddPostToUserCommandHandler;
use App\Domain\Service\AssertService;
use App\Infrastructure\Http\Request;

class AddPostToUserAction extends BaseAction
{
    private CommandHandlerInterface $handler;

    public function __construct()
    {
        $this->handler = new AddPostToUserCommandHandler();
    }

    public function __invoke(Request $request)
    {
        $userId = $request->post('user_id');
        AssertService::string($userId, message: 'User ID must be a string');
        $title = $request->post('title');
        AssertService::string($title, message: 'Title must be a string');
        $content = $request->post('content');
        AssertService::string($content, message: 'Content must be a string');
        $command = new AddPostToUserCommand($userId, $title, $content);
        $result = ($this->handler)($command);

        return $this->responseSuccess($result)->asJson();
    }

}