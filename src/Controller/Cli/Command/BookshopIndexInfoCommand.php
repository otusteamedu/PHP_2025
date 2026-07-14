<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use Elastic\Elasticsearch\ClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('bookshop:index:info')]
class BookshopIndexInfoCommand extends Command
{
    public function __construct(
        private readonly ClientInterface $esClient,
    ) {
        parent::__construct();
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option] string $indexName = 'otus-shop',
    ): int {
        $io = new SymfonyStyle($input, $output);

        $clusterInfo = $this->esClient->cluster()->health()->asArray();
        $io->title("<fg=blue>Cluster info</>");
        $this->printClusterInfo($io, $clusterInfo);

        $indexStructure = $this->esClient->indices()->get(['index' => $indexName])->asArray();
        $io->title("<fg=blue>Index structure ($indexName)</>");
        $this->printIndexStructure($io, $indexStructure[$indexName]);

        return Command::SUCCESS;
    }

    private function printClusterInfo(SymfonyStyle $io, array $clusterInfo): void
    {
        $name = $clusterInfo['cluster_name'] ?? 'unknown';
        $status = $clusterInfo['status'] ?? 'unknown';
        $nodesNumber = $clusterInfo['number_of_nodes'] ?? 'unknown';

        $statusColor = match ($status) {
            'green' => 'fg=green',
            'yellow' => 'fg=yellow',
            'red' => 'fg=red',
            default => 'fg=white',
        };

        $io->writeln("<comment>Cluster name:</comment> <fg=green>$name</>");
        $io->writeln("<comment>Cluster status:</comment> <$statusColor>$status</>");
        $io->writeln("<comment>Number of nodes:</comment> <fg=green>$nodesNumber</>");
    }

    private function printIndexStructure(
        SymfonyStyle $io,
        array $data,
        string $prefix = '',
        ?string $parentKey = null
    ): void {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $io->writeln("$prefix<info>$key:</info>");
                $this->printIndexStructure($io, $value, $prefix . '  ', $key);
            } elseif ($key === 'creation_date' && is_numeric($value)) {
                $timestamp = (int) ($value / 1000);
                $formattedDate = date('Y-m-d H:i:s', $timestamp);
                $io->writeln("$prefix  <comment>$key:</comment> <fg=green>$formattedDate</>");
            } elseif ($key === 'created' && $parentKey === 'version') {
                $readableVersion = $this->decodeElasticsearchVersion((int) $value);
                $io->writeln("$prefix  <comment>$key:</comment> <fg=white>$value</> (<fg=green>$readableVersion</>)");
            } elseif (is_bool($value)) {
                $boolValue = $value ? '<info>true</info>' : '<error>false</error>';
                $io->writeln("$prefix  <comment>$key:</comment> $boolValue");
            } else {
                $io->writeln("$prefix  <comment>$key:</comment> <fg=white>$value</>");
            }
        }
    }

    private function decodeElasticsearchVersion(int $versionCode): string
    {
        $major = (int) ($versionCode / 1000000);
        $minor = (int) (($versionCode % 1000000) / 10000);
        $patch = $versionCode % 10000;

        return "$major.$minor.$patch";
    }
}
