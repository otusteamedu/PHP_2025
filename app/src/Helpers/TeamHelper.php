<?php

namespace App\Helpers;

use App\Collections\TeamsCollection;
use LucidFrame\Console\ConsoleTable;

/**
 * Class TeamHelper
 * @package App\Helpers
 */
class TeamHelper
{
    /**
     * @param TeamsCollection|array $teams
     * @param string $title
     * @return void
     */
    public static function printTeams(TeamsCollection|array $teams, string $title = ''): void
    {
        if ($title) {
            echo $title . PHP_EOL;
        }

        $table = new ConsoleTable();
        foreach ($teams as $key => $team) {
            if ($key === 0) {
                $headers = array_keys($team->getAttributes());
                $headers[] = 'players';
                $table->setHeaders($headers);
            }

            $row = array_values($team->getAttributes());
            $row[] = implode('; ', array_map(fn($player) => $player->getName(), $team->getPlayers()));
            $table->addRow($row);
        }

        $table->display();
    }
}
