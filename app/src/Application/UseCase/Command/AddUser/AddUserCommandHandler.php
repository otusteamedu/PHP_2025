<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\AddUser;

use App\Application\Command\CommandHandlerInterface;
use App\Domain\Factory\UserFactory;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Repository\UserRepository;

class AddUserCommandHandler implements CommandHandlerInterface
{
    private UserFactory $userFactory;
    private UserRepositoryInterface $userRepository;

    public function __construct()
    {
        $this->userFactory = new UserFactory();
        $this->userRepository = new UserRepository();
    }

    public function __invoke(AddUserCommand $command): string
    {
        $user = $this->userFactory->create($command->email, $command->name);
        $this->userRepository->add($user);

        return $user->id;
    }

}