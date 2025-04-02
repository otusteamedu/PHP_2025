<?php

declare(strict_types=1);

namespace App\Entities;

use App\DataMappers\Proxy\PlayerProxy;

/**
 * Class Team
 * @package App\Entities
 */
class Team extends AbstractEntity
{
    /**
     * @var PlayerProxy
     */
    private PlayerProxy $playerProxy;

    /**
     * @param int|null $id
     * @param string $name
     */
    public function __construct(?int $id, private string $name)
    {
        parent::__construct($id);
        $this->playerProxy = new PlayerProxy();
    }

    /**
     * @param array $data
     * @return Team
     */
    public static function create(array $data): static
    {
        return new Team(
            $data['id'] ?? null,
            $data['name']
        );
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array
    {
        return $this->playerProxy->getPlayers($this);
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
}
