<?php

declare(strict_types=1);

namespace App\DataMappers;

use App\Collections\PlayersCollection;
use App\Components\IdentityMap;
use App\Entities\Player;
use App\Entities\Team;
use DomainException;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Class PlayerMapper
 * @package App\DataMappers
 */
class PlayerMapper extends AbstractMapper
{
    /**
     * @var PDOStatement
     */
    protected PDOStatement $findByTeamStatement;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->findByTeamStatement = $this->prepareFindByTeamStatement();
    }

    /**
     * @param Player $player
     * @return Player
     * @throws PDOException
     */
    public function insert(Player $player): Player
    {
        $this->insertStatement->execute([
            ':team_id' => $player->getTeamId(),
            ':number' => $player->getNumber(),
            ':name' => $player->getName(),
            ':age' => $player->getAge(),
            ':height' => $player->getHeight(),
            ':weight' => $player->getWeight(),
        ]);

        $player->setId((int)$this->pdo->lastInsertId());

        IdentityMap::add($player);

        return $player;
    }

    /**
     * @param Player $player
     * @return void
     * @throws PDOException
     */
    public function update(Player $player): void
    {
        $this->updateStatement->execute([
            ':team_id' => $player->getTeamId(),
            ':number' => $player->getNumber(),
            ':name' => $player->getName(),
            ':age' => $player->getAge(),
            ':height' => $player->getHeight(),
            ':weight' => $player->getWeight(),
            ':id' => $player->getId(),
        ]);

        IdentityMap::add($player);
    }

    /**
     * @param Player $player
     * @return void
     * @throws PDOException
     */
    public function delete(Player $player): void
    {
        $this->deleteStatement->execute([
            ':id' => $player->getId(),
        ]);

        IdentityMap::delete($player);
    }

    /**
     * @param int $id
     * @return Player|null
     * @throws PDOException
     */
    public function findById(int $id): ?Player
    {
        /** @var Player|null $player */
        $player = IdentityMap::get(Player::class, $id);
        if ($player) {
            return $player;
        }

        $this->findByIdStatement->execute([
            ':id' => $id,
        ]);

        $result = $this->findByIdStatement->fetch();
        if ($result === false) {
            return null;
        }

        $player = Player::create($result);
        IdentityMap::add($player);

        return $player;
    }

    /**
     * @return PlayersCollection
     * @throws PDOException
     */
    public function findAll(): PlayersCollection
    {
        $this->findAllStatement->execute();

        $result = $this->findAllStatement->fetchAll();
        if ($result === false) {
            throw new DomainException('Result fetch error');
        }

        $playersCollection = new PlayersCollection();
        foreach ($result as $row) {
            $player = Player::create($row);
            IdentityMap::add($player);
            $playersCollection->add($player);
        }

        return $playersCollection;
    }

    /**
     * @param Team $team
     * @return Player[]
     * @throws PDOException
     */
    public function findByTeam(Team $team): array
    {
        $this->findByTeamStatement->execute([
            ':team_id' => $team->getId(),
        ]);

        $result = $this->findByTeamStatement->fetchAll();
        if ($result === false) {
            return [];
        }

        $players = [];
        foreach ($result as $row) {
            $player = Player::create($row);
            IdentityMap::add($player);
            $players[] = $player;
        }

        return $players;
    }

    /**
     * @return false|PDOStatement
     */
    protected function prepareFindByTeamStatement(): false|PDOStatement
    {
        return $this->pdo->prepare($this->getFindByTeamStatementQuery());
    }

    /**
     * @inheritdoc
     */
    protected function getInsertStatementQuery(): string
    {
        return 'INSERT INTO players (team_id, number, name, age, height, weight) 
                VALUES (:team_id, :number, :name, :age, :height, :weight)';
    }

    /**
     * @inheritdoc
     */
    protected function getUpdateStatementQuery(): string
    {
        return 'UPDATE players 
                SET team_id = :team_id,
                    number = :number,
                    name = :name,
                    age = :age,
                    height = :height,
                    weight = :weight
                 WHERE id = :id';
    }

    /**
     * @inheritdoc
     */
    protected function getDeleteStatementQuery(): string
    {
        return 'DELETE FROM players WHERE id = :id';
    }

    /**
     * @inheritdoc
     */
    protected function getFindByIdStatementQuery(): string
    {
        return 'SELECT * FROM players WHERE id = :id';
    }

    /**
     * @inheritdoc
     */
    protected function getFindAllStatementQuery(): string
    {
        return 'SELECT * FROM players ORDER BY id';
    }

    /**
     * @return string
     */
    protected function getFindByTeamStatementQuery(): string
    {
        return 'SELECT * FROM players WHERE team_id = :team_id ORDER BY id';
    }
}
