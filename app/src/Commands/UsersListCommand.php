<?php
declare(strict_types=1);

namespace App\Commands;

use App\Users\User;
use App\Users\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('users:list', description: 'List all users using Data Mapper pattern')]
final class UsersListCommand extends Command
{
    public function __construct(private readonly UserRepository $users)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $list = $this->users->findAll();
        if (!$list) {
            $output->writeln('No users found');
            return self::SUCCESS;
        }

        foreach ($list as $user) {``
            assert($user instanceof User);
            $postsCount = $user->posts()->count();;
            $output->writeln(sprintf('#%d: %s <%s> have %d posts', $user->id, $user->name, $user->email, $postsCount));
        }

        return self::SUCCESS;
    }
}
