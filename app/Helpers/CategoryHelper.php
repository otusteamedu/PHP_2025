<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Helpers;

use LucidFrame\Console\ConsoleTable;
use Zibrov\OtusPhp2025\Collections\CategoryCollection;

class CategoryHelper
{

    public static function printCategory(CategoryCollection|array $categories): void
    {
        $table = new ConsoleTable();
        foreach ($categories as $key => $category) {
            if ($key === 0) {
                $headers = array_keys($category->getAttributes());
                $headers[] = 'offers';
                $table->setHeaders($headers);
            }

            $row = array_values($category->getAttributes());
            $row[] = implode('; ', array_map(static fn($offers) => $offers->getName(), $category->getOffers()));
            $table->addRow($row);
        }


        $table->display();
    }

    public static function getCategory(CategoryCollection|array $categories): array
    {
        $arCategory = [];
        foreach ($categories as $category) {
            $arCategory[] = $category->getAttributes();
        }

        return $arCategory;
    }
}
