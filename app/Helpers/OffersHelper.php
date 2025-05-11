<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Helpers;

use LucidFrame\Console\ConsoleTable;
use Zibrov\OtusPhp2025\Collections\OffersCollection;

class OffersHelper
{

    public static function printOffers(OffersCollection|array $offersCollection): void
    {
        $table = new ConsoleTable();
        foreach ($offersCollection as $key => $offers) {
            if ($key === 0) {
                $table->setHeaders(array_keys($offers->getAttributes()));
            }
            $row = array_values($offers->getAttributes());
            $table->addRow($row);
        }

        $table->display();
    }

    public static function getOffers(OffersCollection|array $offersCollection): array
    {
        $arOffers = [];
        foreach ($offersCollection as $offers) {
            $arOffers[] = $offers->getAttributes();
        }

        return $arOffers;
    }
}
