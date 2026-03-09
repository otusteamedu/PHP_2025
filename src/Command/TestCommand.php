<?php

declare(strict_types=1);

namespace App\Command;

use App\Infrastructure\Elasticsearch\Client\ElasticsearchClientProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:test')]
class TestCommand extends Command
{
    public function __invoke(OutputInterface $output): int
    {
        $esClient = ElasticsearchClientProvider::get();
        $response = $esClient->cluster()->health()->asArray();

        var_dump($response['status']);

        return Command::SUCCESS;
    }
}
