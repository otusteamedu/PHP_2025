<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\EventInfoName;

class EventInfo
{
    /**
     * @var positive-int
     */
    private int $id;
    /**
     * @var EventInfoName
     */
    private EventInfoName $name;

    /**
     * @param int $id
     * @param EventInfoName $name
     */
    public function __construct(int $id, EventInfoName $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return EventInfoName
     */
    public function getName(): EventInfoName
    {
        return $this->name;
    }
}
