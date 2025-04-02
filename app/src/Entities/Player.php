<?php

declare(strict_types=1);

namespace App\Entities;

use App\DataMappers\Proxy\TeamProxy;

/**
 * Class Player
 * @package App\Entities
 */
class Player extends AbstractEntity
{
    /**
     * @var TeamProxy
     */
    private TeamProxy $teamProxy;

    /**
     * @param int|null $id
     * @param int|null $teamId
     * @param int $number
     * @param string $name
     * @param int $age
     * @param int $height
     * @param int $weight
     */
    public function __construct(
        ?int $id,
        private ?int $teamId,
        private int $number,
        private string $name,
        private int $age,
        private int $height,
        private int $weight
    ) {
        parent::__construct($id);
        $this->teamProxy = new TeamProxy();
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return [
            'id' => $this->getId(),
            'team_id' => $this->getTeamId(),
            'number' => $this->getNumber(),
            'name' => $this->getName(),
            'age' => $this->getAge(),
            'height' => $this->getHeight(),
            'weight' => $this->getWeight(),
        ];
    }

    /**
     * @param array $data
     * @return Player
     */
    public static function create(array $data): static
    {
        return new Player(
            $data['id'] ?? null,
            $data['team_id'] ?? null,
            $data['number'],
            $data['name'],
            $data['age'],
            $data['height'],
            $data['weight'],
        );
    }

    /**
     * @return Team|null
     */
    public function getTeam(): ?Team
    {
        return $this->teamProxy->getTeam($this);
    }

    /**
     * @return int|null
     */
    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    /**
     * @param int $teamId
     * @return void
     */
    public function setTeamId(int $teamId): void
    {
        $this->teamId = $teamId;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return void
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return void
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return void
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     * @return void
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }
}
