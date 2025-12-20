<?php
declare(strict_types=1);

namespace App\Command;

use App\Repository\BookRepository;
use App\Table\BookTableRender;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'shop:search',
    description: 'Выполняет поиск книг по заданным параметрам'
)]
class SearchCommand extends Command
{
    private BookRepository $repository;

    /**
     * @param BookRepository $repository
     */
    public function __construct(BookRepository $repository)
    {
        parent::__construct('shop:search');
        $this->repository = $repository;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addOption('title', null, InputOption::VALUE_REQUIRED, 'Название книги')
            ->addOption('category', null, InputOption::VALUE_REQUIRED, 'Категория')
            ->addOption('max-price', null, InputOption::VALUE_REQUIRED, 'Максимальная цена')
            ->addOption('in-stock', null, InputOption::VALUE_NONE, 'Наличие')
            ->setHelp('Пример:' . PHP_EOL .
                'php bin/console shop:search --title="рыцари" --category="исторический роман" --max-price=2000 --in-stock'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $results = $this->repository->search(
            $input->getOption('title'),
            $input->getOption('category'),
            (int)$input->getOption('max-price'),
            $input->getOption('in-stock')
        );

        BookTableRender::render($output, $results);

        return Command::SUCCESS;
    }
}
