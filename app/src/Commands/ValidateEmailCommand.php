<?php
declare(strict_types = 1);

namespace App\Commands;

use App\Validators\EmailValidator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand('email:validate', description: 'Validate email')]
final class ValidateEmailCommand extends Command
{
    public function __construct(
        private readonly EmailValidator $emailValidator,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Email to validate');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        if (!$this->emailValidator->validate($email, true)) {
            $output->writeln('Invalid email');
            return self::FAILURE;
        }

        $output->writeln('Valid email');
        return self::SUCCESS;
    }
}
