<?php

class Hall
{
    private int $id;
    private int $cinemaId;
    private string $name;
    private int $capacity;

    public function __construct(int $id, int $cinemaId, string $name, int $capacity)
    {
        $this->id = $id;
        $this->cinemaId = $cinemaId;
        $this->name = $name;
        $this->capacity = $capacity;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCinemaId(): int
    {
        return $this->cinemaId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }
}
