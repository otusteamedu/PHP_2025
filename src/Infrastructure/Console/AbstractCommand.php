<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Infrastructure\Console;

use Dinargab\Homework14\Application\DTO\TableRowInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @param OutputInterface $output
     * @param TableRowInterface $items
     *
     * @return void
     */
    protected function renderTable(OutputInterface $output, array $items): void
    {
        $table = new Table($output);

        $firstElement = reset($items);

        $table->setHeaders($firstElement::getTableHeaders());

        $rows = [];
        /** @var TableRowInterface $item */
        foreach ($items as $item) {
            $rows[] = $item->toTableRow();
        }

        $table->addRows($rows);
        $table->render();
    }
}