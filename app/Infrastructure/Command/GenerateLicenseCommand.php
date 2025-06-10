<?php
declare(strict_types=1);

namespace Infrastructure\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Application\UseCase\GenerateLicense\GenerateLicenseUseCase;
use Application\UseCase\GenerateLicense\GenerateLicenseRequest;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateLicenseCommand extends Command
{
    public function __construct(
        private GenerateLicenseUseCase $useCase,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('userId', InputArgument::REQUIRED, 'UserId')
            ->addArgument('serviceId', InputArgument::REQUIRED, 'ServiceId')
            ->addArgument('createDate', InputArgument::REQUIRED, 'CreateDate')
            ->addArgument('period', InputArgument::REQUIRED, 'Period');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $submitLicenseRequest = new GenerateLicenseRequest(
                $input->getArgument('name'),
                $input->getArgument('phone'),
                $input->getArgument('createDate'),
                $input->getArgument('period')
            );
            $submitLicenseRequest = ($this->useCase)($submitLicenseRequest);
            $output->writeln('License ID: ' . $submitLicenseRequest->id);
            $output->writeln('Serial Number ID: ' . $submitLicenseRequest->serialNumber);
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }
    }
}