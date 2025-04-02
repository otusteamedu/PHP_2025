<?php

namespace App\Helpers;

use App\Collections\PlayersCollection;
use LucidFrame\Console\ConsoleTable;

/**
 * Class PlayerHelper
 * @package App\Helpers
 */
class PlayerHelper
{
    /**
     * @param PlayersCollection|array $players
     * @param string $title
     * @return void
     */
    public static function printPlayers(PlayersCollection|array $players, string $title = ''): void
    {
        if ($title) {
            echo $title . PHP_EOL;
        }

        $table = new ConsoleTable();
        foreach ($players as $key => $player) {
            if ($key === 0) {
                $headers = array_keys($player->getAttributes());
                $headers[] = 'team';
                $table->setHeaders($headers);
            }

            $row = array_values($player->getAttributes());
            $row[] = $player->getTeam()?->getName();

            $row = array_map(fn($value) => $value === null ? 'NULL' : $value, $row);
            $table->addRow($row);
        }

        $table->display();
    }
}
