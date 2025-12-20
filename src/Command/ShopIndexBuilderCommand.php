<?php
declare(strict_types=1);

namespace App\Command;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use JsonException;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'shop:index:build',
    description: 'Создаёт индекс и загружает данные из JSON файла'
)]
class ShopIndexBuilderCommand extends Command
{
    private const string ES_INDEX = 'otus-shop';

    private const int BULK_OPERATIONS_BATCH_SIZE  = 1000;

    private Client $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct('shop:index:build');
        $this->client = $client;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Путь до файла')
            ->setHelp('Пример:' . PHP_EOL .
                'php bin/console shop:index:build --file=otus-shop.json'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getOption('file');

        if ($file === null || !is_readable($file)) {
            $output->writeln('Файл не найден или недоступен для чтения');

            return Command::FAILURE;
        }

        $this->createIndex($output);
        $this->bulkIndex($file, $output);

        $output->writeln('Индексация завершена');

        return Command::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @return void
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    private function createIndex(OutputInterface $output): void
    {
        if ($this->client->indices()->exists(['index' => self::ES_INDEX])->asBool()) {
            $this->client->indices()->delete(['index' => self::ES_INDEX]);
            $output->writeln('Индекс удален');
        }

        $this->client->indices()->create([
            'index' => self::ES_INDEX,
            'body' => [
                'settings' => [
                    'analysis' => [
                        'normalizer' => [
                            'lowercase_normalizer' => [
                                'type' => 'custom',
                                'filter' => ['lowercase'],
                            ],
                        ],
                        'analyzer' => [
                            'ru_analyzer' => [
                                'type' => 'standard',
                                'stopwords' => '_russian_',
                                'filter' => ['lowercase', 'russian_morphology'],
                            ],
                        ],
                        'filter' => [
                            'russian_morphology' => [
                                'type' => 'stemmer',
                                'language' => 'russian',
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'sku' => ['type' => 'keyword'],
                        'title' => [
                            'type' => 'text',
                            'analyzer' => 'ru_analyzer',
                        ],
                        'category' => [
                            'type' => 'keyword',
                            'normalizer' => 'lowercase_normalizer',
                        ],
                        'price' => ['type' => 'integer'],
                        'stock' => [
                            'type' => 'nested',
                            'properties' => [
                                'shop' => ['type' => 'keyword'],
                                'stock' => ['type' => 'integer'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $output->writeln('Индекс создан');
    }

    /**
     * @param string $file
     * @param OutputInterface $output
     * @return void
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws JsonException
     */
    private function bulkIndex(string $file, OutputInterface $output): void
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            throw new RuntimeException('Не удалось открыть файл');
        }

        $batch = [];
        $indexed = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $batch[] = json_decode($line, true, flags: JSON_THROW_ON_ERROR);

            if (count($batch) >= self::BULK_OPERATIONS_BATCH_SIZE) {
                $this->client->bulk(['body' => $batch]);
                $indexed += intdiv(count($batch), 2);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $this->client->bulk(['body' => $batch]);
            $indexed += intdiv(count($batch), 2);
        }

        fclose($handle);

        $output->writeln('Загружено документов: ' . $indexed);
    }
}
