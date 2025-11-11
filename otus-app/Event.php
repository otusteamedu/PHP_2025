<?php

namespace App;

class Event
{
    private int $id;
    private string $name;
    private EventConfigProxy $eventConfigProxy;

    public function __construct(
        int $id,
        string $name,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->eventConfigProxy = new EventConfigProxy();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return null|EventConfig[] */
    public function getEventConfigList(): ?array
    {
        echo 'checking' . "<br>";

        return $this->eventConfigProxy->getEventConfigList($this);
    }
}
