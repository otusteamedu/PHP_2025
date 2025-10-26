<?php

declare(strict_types=1);

namespace App\Proxy;

use App\Application;
use App\DataMappers\TeamMapper;
use App\Entities\Player;
use App\Entities\Team;

/**
 * Class TeamProxy
 * @package App\Proxy
 */
class TeamProxy
{
    /**
     * @var TeamMapper
     */
    private TeamMapper $mapper;
    /**
     * @var Team|null|false
     */
    private Team|null|false $team = false;

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
        $teamId = $player->getTeamId();

        if ($this->team === false) {
            $this->team = $teamId ? $this->mapper->findById($teamId) : null;
        }

        return $this->team;
    }
}
