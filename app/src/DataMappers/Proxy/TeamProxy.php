<?php

declare(strict_types=1);

namespace App\DataMappers\Proxy;

use App\Application;
use App\DataMappers\TeamMapper;
use App\Entities\Player;
use App\Entities\Team;

/**
 * Class TeamProxy
 * @package App\DataMappers\Proxy
 */
class TeamProxy
{
    /**
     * @var TeamMapper
     */
    private TeamMapper $mapper;
    /**
     * @var Team|null
     */
    private ?Team $team = null;

    /**
     *
     */
    public function __construct()
    {
        $this->mapper = new TeamMapper(Application::$app->getPDO());
    }

    /**
     * @param Player $player
     * @return Team|null
     */
    public function getTeam(Player $player): ?Team
    {
        if (!$player->getTeamId()) {
            return null;
        }

        if ($this->team === null) {
            $this->team = $this->mapper->findById($player->getTeamId());
        }

        return $this->team;
    }
}
