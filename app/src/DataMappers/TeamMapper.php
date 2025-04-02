<?php

declare(strict_types=1);

namespace App\DataMappers;

use App\Collections\TeamsCollection;
use App\Components\IdentityMap;
use App\Entities\Team;
use DomainException;
use PDOException;

/**
 * Class TeamMapper
 * @package App\DataMappers
 */
class TeamMapper extends AbstractMapper
{
    /**
     * @param Team $team
     * @return Team
     * @throws PDOException
     */
    public function insert(Team $team): Team
    {
        $this->insertStatement->execute([
            ':name' => $team->getName(),
        ]);

        $team->setId((int)$this->pdo->lastInsertId());

        IdentityMap::add($team);

        return $team;
    }

    /**
     * @param Team $team
     * @return void
     * @throws PDOException
     */
    public function update(Team $team): void
    {
        $this->updateStatement->execute([
            ':name' => $team->getName(),
            ':id' => $team->getId(),
        ]);

        IdentityMap::add($team);
    }

    /**
     * @param Team $team
     * @return void
     * @throws PDOException
     */
    public function delete(Team $team): void
    {
        $this->deleteStatement->execute([
            ':id' => $team->getId(),
        ]);

        IdentityMap::delete($team);
    }

    /**
     * @param int $id
     * @return Team|null
     * @throws PDOException
     */
    public function findById(int $id): ?Team
    {
        /** @var Team|null $team */
        $team = IdentityMap::get(Team::class, $id);
        if ($team) {
            return $team;
        }

        $this->findByIdStatement->execute([
            ':id' => $id,
        ]);

        $result = $this->findByIdStatement->fetch();
        if ($result === false) {
            return null;
        }

        $team = Team::create($result);
        IdentityMap::add($team);

        return $team;
    }

    /**
     * @return TeamsCollection
     * @throws PDOException
     */
    public function findAll(): TeamsCollection
    {
        $this->findAllStatement->execute();

        $result = $this->findAllStatement->fetchAll();
        if ($result === false) {
            throw new DomainException('Result fetch error');
        }

        $teamsCollection = new TeamsCollection();
        foreach ($result as $row) {
            $team = Team::create($row);
            IdentityMap::add($team);
            $teamsCollection->add($team);
        }

        return $teamsCollection;
    }

    /**
     * @inheritdoc
     */
    protected function getInsertStatementQuery(): string
    {
        return 'INSERT INTO teams (name) VALUES (:name)';
    }

    /**
     * @inheritdoc
     */
    protected function getUpdateStatementQuery(): string
    {
        return 'UPDATE teams SET name = :name WHERE id = :id';
    }

    /**
     * @inheritdoc
     */
    protected function getDeleteStatementQuery(): string
    {
        return 'DELETE FROM teams WHERE id = :id';
    }

    /**
     * @inheritdoc
     */
    protected function getFindByIdStatementQuery(): string
    {
        return 'SELECT * FROM teams WHERE id = :id';
    }

    /**
     * @inheritdoc
     */
    protected function getFindAllStatementQuery(): string
    {
        return 'SELECT * FROM teams ORDER BY id';
    }
}
