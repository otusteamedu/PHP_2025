<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Console;

use Dinargab\Homework14\Application\DTO\GetBySkuRequestDTO;
use Dinargab\Homework14\Application\UseCase\GetBySkuUseCase;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:get-by-id', description: 'Get item by identificator')]
class GetByIdCommand extends AbstractCommand
{
    public function __construct(
        private GetBySkuUseCase $getBySkuUseCase
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Получить элемент по индентификатору')
             ->addArgument("id", InputArgument::REQUIRED, "ID элемента");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $requestDto = new GetBySkuRequestDTO(
            sku: $input->getArgument("id")
        );

        try {
            $bookDto = ($this->getBySkuUseCase)($requestDto);

            if ( ! $bookDto) {
                $output->writeln("<error>Элемент не найден.</error>");

                return Command::FAILURE;
            }

            $this->renderTable($output, [$bookDto]);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>Ошибка: " . $e->getMessage() . "</error>");

            return Command::FAILURE;
        }
    }
}