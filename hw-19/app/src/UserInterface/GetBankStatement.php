<?php

declare(strict_types=1);

namespace App\UserInterface;

use App\Application\GetBankStatement\GetBankStatementRequest;
use App\Application\GetBankStatement\GetBankStatementHandler;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'get-bank-statement',
    description: 'Получение банковской выписки за указанные даты'
)]
class GetBankStatement extends Command
{
    public function __construct(
        public GetBankStatementHandler $getBankStatementHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('account', InputArgument::REQUIRED, 'Номер счёта')
            ->addArgument('dateFrom', InputArgument::REQUIRED, 'Дата начала периода (YYYY-MM-DD)')
            ->addArgument('dateTo', InputArgument::REQUIRED, 'Дата окончания периода (YYYY-MM-DD)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $account = $input->getArgument('account');
        $dateFrom = $input->getArgument('dateFrom');
        $dateTo = $input->getArgument('dateTo');

        $io->title('Получение банковской выписки');
        $io->section('Параметры');
        $io->listing([
            "Счёт: $account",
            "Период: $dateFrom — $dateTo",
        ]);

        $this->getBankStatementHandler->execute(
            new GetBankStatementRequest(
                account: $account,
                dateFrom: new DateTimeImmutable($dateFrom),
                dateTo: new DateTimeImmutable($dateTo),
            )
        );

        $io->success('Запрос отправлен. Данные будут обработаны сервисом.');

        return Command::SUCCESS;
    }
}
