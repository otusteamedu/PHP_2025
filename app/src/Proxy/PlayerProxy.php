<?php

declare(strict_types=1);

namespace App\Proxy;

use App\Application;
use App\DataMappers\PlayerMapper;
use App\Entities\Player;
use App\Entities\Team;

/**
 * Class PlayerProxy
 * @package App\Proxy
 */
class PlayerProxy
{
    /**
     * @var PlayerMapper
     */
    private PlayerMapper $mapper;
    /**
     * @var Player[]|null
     */
    private ?array $players = null;

    /**
     *
     */
    public function __construct()
    {
        $this->mapper = new PlayerMapper(Application::$app->getPDO());
    }

    /**
     * @param Team $team
     * @return Player[]
     */
    public function getPlayers(Team $team): array
    {
        if ($this->players === null) {
            $this->players = $this->mapper->findByTeam($team);
        }

        return $this->players;
    }
}
