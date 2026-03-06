<?php

declare(strict_types=1);

namespace Pryaniki\App\Presentation\Views;

class TableView
{
    private int $titleColumnWidth;
    private int $categoryColumnWidth;
    private int $priceColumnWidth;

    const COLUMN_SEPARATOR = ' | ';

    /**
     * @param int $titleColumnWidth
     * @param int $categoryColumnWidth
     * @param int $priceColumnWidth
     */
    public function __construct(int $titleColumnWidth = 60, int $categoryColumnWidth = 25, int $priceColumnWidth = 8)
    {
        $this->titleColumnWidth = $titleColumnWidth;
        $this->categoryColumnWidth = $categoryColumnWidth;
        $this->priceColumnWidth = $priceColumnWidth;
        //todo принимать объект с настройками
        // нужно получить список ключей, которые нужно взять из _source и ширину столбца
        // $config =
        // title => [
        // 'column-size' => 60,
        // 'column-name' => 'Название',
        // ],
        // ...
        //
        // В printHead вместо 6 вычислять $countSeparator = (count($config) - 1) * 2;


    }

    public function printTable(array $hits): void
    {
        $this->printHead();
        foreach ($hits as $hit) {
            $src = $hit['_source'];

            echo
                $this->limitColumnSize($src['title'], $this->titleColumnWidth) . self::COLUMN_SEPARATOR .
                $this->limitColumnSize($src['category'], $this->categoryColumnWidth) . self::COLUMN_SEPARATOR .
                $this->limitColumnSize((string)$src['price'], $this->priceColumnWidth) . PHP_EOL;
        }
    }

    private function printHead(): void
    {
        echo
            $this->limitColumnSize('Название', $this->titleColumnWidth) . self::COLUMN_SEPARATOR .
            $this->limitColumnSize('Категория', $this->categoryColumnWidth) . self::COLUMN_SEPARATOR .
            $this->limitColumnSize('Цена', $this->priceColumnWidth) . PHP_EOL;

        echo str_repeat('-', $this->titleColumnWidth + $this->categoryColumnWidth + $this->priceColumnWidth + 6) . PHP_EOL;
    }

    private function printRow(): void
    {
        //todo вынести печать таблицы
    }

    private function limitColumnSize(string $text, int $width): string
    {
        $text = mb_strimwidth($text, 0, $width, '…');
        $padLength = $width - mb_strwidth($text);

        return $text . str_repeat(' ', max(0, $padLength));
    }
}