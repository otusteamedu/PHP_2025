<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Console;

use Dinargab\Homework14\Application\DTO\GetBySkuRequestDTO;
use Dinargab\Homework14\Application\UseCase\GetBySkuUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:get-by-sku', description: 'Get book by SKU')]
class GetBySkuCommand extends AbstractCommand
{
    protected static $defaultName = 'app:get-by-sku';

    public function __construct(
        private GetBySkuUseCase $getBySkuUseCase
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Получить книгу по SKU')
             ->addArgument("sku", InputArgument::REQUIRED, "SKU книги");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $requestDto = new GetBySkuRequestDTO(
            sku: $input->getArgument("sku")
        );

        try {
            $bookDto = ($this->getBySkuUseCase)($requestDto);

            if (!$bookDto) {
                $output->writeln("<error>Книга не найдена.</error>");
                return Command::FAILURE;
            }

            $this->renderBooksTable($output, [$bookDto]);
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln("<error>Ошибка: " . $e->getMessage() . "</error>");
            return Command::FAILURE;
        }
    }
}