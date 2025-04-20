<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\AddPostToUser;

use App\Application\Command\CommandHandlerInterface;
use App\Domain\Factory\UserPostFactory;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Repository\UserRepository;

class AddPostToUserCommandHandler implements CommandHandlerInterface
{
    private UserPostFactory $userPostFactory;
    private UserRepositoryInterface $userRepository;

    public function __construct()
    {
        $this->userPostFactory = new UserPostFactory();
        $this->userRepository = new UserRepository();
    }

    public function __invoke(AddPostToUserCommand $command): string
    {
        $user = $this->userRepository->get($command->userId);
        if (!$user) {
            throw new \Exception('User not found');
        }
        $post = $this->userPostFactory->create($user, $command->title, $command->content);
        $user->addPost($post);
        $this->userRepository->add($user);

        return $post->id;
    }

}