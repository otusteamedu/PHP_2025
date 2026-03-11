<?php

declare(strict_types=1);

namespace Pryaniki\App\Presentation\Views;

class TableView
{
    const COLUMN_SEPARATOR = ' | ';
    const BORDER_LINE_SYMBOL = '-';

    private array $config;


    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function printTable(array $data): void
    {
        $this->printHead();
        foreach ($data as $value) {
            $rowData = $value['_source'];
            $this->printRow($rowData);
        }
    }

    private function printHead(): void
    {
        $sizeBorderLine = count($this->config) * 2;
        $rowData = [];

        foreach ($this->config as $columnKey => $columnSetting) {
            $rowData[$columnKey] = $columnSetting['name'];
            $sizeBorderLine += (int)$columnSetting['width'];
        }

        $this->printRow($rowData);
        $this->printBorderLine($sizeBorderLine);
    }

    private function printRow($data): void
    {
        $arRow = [];

        foreach ($this->config as $columnKey => $columnSetting) {
            $value = $data[$columnKey] ?? '';
            $arRow[] = $this->limitColumnSize((string)$value, $columnSetting['width']);
        }

        echo implode(self::COLUMN_SEPARATOR, $arRow) . PHP_EOL;
    }

    private function printBorderLine(int $borderLineSize): void
    {
        echo str_repeat(self::BORDER_LINE_SYMBOL, $borderLineSize) . PHP_EOL;
    }

    private function limitColumnSize(string $text, int $width): string
    {
        $text = mb_strimwidth($text, 0, $width, '…');
        $padLength = $width - mb_strwidth($text);

        return $text . str_repeat(' ', max(0, $padLength));
    }
}