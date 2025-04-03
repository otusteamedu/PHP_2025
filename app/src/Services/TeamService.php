<?php

declare(strict_types=1);

namespace App\Services;

use App\DataMappers\TeamMapper;
use App\Entities\Team;
use App\Helpers\TeamHelper;
use PDO;

/**
 * Class TeamService
 * @package App\Services
 */
class TeamService
{
    /**
     * @var TeamMapper
     */
    private TeamMapper $mapper;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->mapper = new TeamMapper($pdo);
    }

    /**
     * @param Team $team
     * @return Team
     */
    public function create(Team $team): Team
    {
        return $this->mapper->insert($team);
    }

    /**
     * @param Team $team
     * @return void
     */
    public function update(Team $team): void
    {
        $this->mapper->update($team);
    }

    /**
     * @param Team $team
     * @return void
     */
    public function delete(Team $team): void
    {
        $this->mapper->delete($team);
    }

    /**
     * @param int $id
     * @return Team|null
     */
    public function findById(int $id): ?Team
    {
        return $this->mapper->findById($id);
    }

    /**
     * @param Team $team
     * @return bool
     */
    public function testIdentityMap(Team $team): bool
    {
        $team_2 = $this->mapper->findById($team->getId());
        return $team === $team_2;
    }

    /**
     * @return void
     */
    public function printAll(): void
    {
        TeamHelper::printTeams($this->mapper->findAll(), 'teams');
    }

    /**
     * @return array[]
     */
    public function getTeamsDataList(): array
    {
        return [
            [
                'name' => 'Ливерпуль',
            ],
            [
                'name' => 'Реал',
            ],
            [
                'name' => 'ПСЖ',
            ],
        ];
    }
}
