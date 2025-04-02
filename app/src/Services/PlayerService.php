<?php

declare(strict_types=1);

namespace App\Services;

use App\DataMappers\PlayerMapper;
use App\Entities\Player;
use App\Helpers\PlayerHelper;
use PDO;

/**
 * Class PlayerService
 * @package App\Services
 */
class PlayerService
{
    /**
     * @var PlayerMapper
     */
    private PlayerMapper $mapper;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->mapper = new PlayerMapper($pdo);
    }

    /**
     * @param Player $player
     * @return Player
     */
    public function create(Player $player): Player
    {
        return $this->mapper->insert($player);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function update(Player $player): void
    {
        $this->mapper->update($player);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function delete(Player $player): void
    {
        $this->mapper->delete($player);
    }

    /**
     * @param int $id
     * @return Player|null
     */
    public function findById(int $id): ?Player
    {
        return $this->mapper->findById($id);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function testIdentityMap(Player $player): bool
    {
        $player_2 = $this->mapper->findById($player->getId());
        return $player === $player_2;
    }

    /**
     * @return void
     */
    public function printAll(): void
    {
        PlayerHelper::printPlayers($this->mapper->findAll(), 'players');
    }

    /**
     * @return array[]
     */
    public function getPlayersDataList(): array
    {
        return [
            // Ливерпуль
            [
                'team_id' => 1,
                'number' => 11,
                'name' => 'Мохамед Салах',
                'age' => 32,
                'height' => 175,
                'weight' => 71,
            ],
            [
                'team_id' => 1,
                'number' => 4,
                'name' => 'Вирджил ван Дейк',
                'age' => 33,
                'height' => 195,
                'weight' => 92,
            ],

            // Реал
            [
                'team_id' => 2,
                'number' => 9,
                'name' => 'Килиан Мбаппе',
                'age' => 26,
                'height' => 178,
                'weight' => 73,
            ],
            [
                'team_id' => 2,
                'number' => 7,
                'name' => 'Винисиус Жуниор',
                'age' => 24,
                'height' => 177,
                'weight' => 73,
            ],

            // ПСЖ
            [
                'team_id' => 3,
                'number' => 10,
                'name' => 'Усман Дембеле',
                'age' => 27,
                'height' => 178,
                'weight' => 67,
            ],
            [
                'team_id' => 3,
                'number' => 7,
                'name' => 'Хвича Кварацхелия',
                'age' => 24,
                'height' => 183,
                'weight' => 76,
            ],
            [
                'team_id' => 3,
                'number' => 1,
                'name' => 'Джанлуиджи Доннарумма',
                'age' => 26,
                'height' => 196,
                'weight' => 90,
            ],
        ];
    }
}
