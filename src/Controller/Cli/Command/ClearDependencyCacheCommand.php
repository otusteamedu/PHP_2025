<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use App\Core\Storage\KeyValue\Shared\DependencyCacheInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('cache:clear:dependencies')]
class ClearDependencyCacheCommand extends Command
{
    public function __construct(
        private readonly DependencyCacheInterface $cache,
    ) {
        parent::__construct();
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->cache->clear();
            $output->writeln('<info>Dependencies cache cleared successfully.</info>');
            return Command::SUCCESS;
        } catch (\RuntimeException $e) {
            $output->writeln(sprintf('<error>Failed to clear dependencies cache: %s</error>', $e->getMessage()));
            return Command::FAILURE;
        }
    }
}
