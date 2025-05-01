<?php
declare(strict_types=1);


namespace App\User\Application\UseCase\Command\AddUser;

use App\Shared\Application\Command\CommandHandlerInterface;
use App\User\Domain\Factory\UserFactory;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Infrastructure\Repository\UserRepository;

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